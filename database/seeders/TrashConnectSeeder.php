<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Participant;
use App\Models\Season;
use App\Models\Show;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrashConnectSeeder extends Seeder
{
    /**
     * Versucht, eine Ganzzahl-ID aus gemischten Werten zu bekommen.
     */
    protected function intOrNull($value): ?int
    {
        if ($value === null || $value === '') return null;
        return is_numeric($value) ? (int)$value : null;
    }

    /**
     * Slug aus String erzeugen, Fallback mit uniqid.
     */
    protected function safeSlug(?string $value, string $prefix): string
    {
        $slug = $value ? Str::slug($value) : null;
        return $slug && $slug !== '' ? $slug : $prefix.'-'.uniqid();
    }

    public function run(): void
    {
        // --- 1) JSON laden ---------------------------------------------------
        $showsPath      = resource_path('data/shows.json');
        $candidatesPath = resource_path('data/candidates.json');

        if (! File::exists($showsPath)) {
            $this->command->error("shows.json nicht gefunden unter: {$showsPath}");
            return;
        }

        $showsJson = File::get($showsPath);
        $showsArr  = json_decode($showsJson, true);

        if (! is_array($showsArr)) {
            $this->command->error("shows.json ist kein Array oder fehlerhaftes JSON.");
            return;
        }

        // Kandidaten optional vorladen (falls vorhanden)
        $legacyIdToCandidateId = [];
        if (File::exists($candidatesPath)) {
            $candJson = File::get($candidatesPath);
            $candArr  = json_decode($candJson, true);
            if (is_array($candArr)) {
                foreach ($candArr as $i => $c) {
                    $name = Arr::get($c, 'name');
                    $slug = Arr::get($c, 'slug');
                    $legacyId = Arr::get($c, 'id');

                    if (! $name) {
                        Log::warning("Candidate-Datensatz #{$i} ohne 'name' übersprungen.");
                        continue;
                    }

                    // Slug ggf. generieren
                    $slug = $slug ?: $this->safeSlug($name, 'cand');

                    // upsert
                    $model = Candidate::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'bio'  => Arr::get($c, 'bio'),
                            'birth_date' => Arr::get($c, 'birth_date'),
                            'social_media' => Arr::get($c, 'social_media'),
                            'meta_title' => Arr::get($c, 'meta_title'),
                            'meta_description' => Arr::get($c, 'meta_description'),
                        ]
                    );

                    if ($legacyId !== null) {
                        $legacyIdToCandidateId[(int)$legacyId] = $model->id;
                    }
                }
            }
        }

        // --- 2) Shows + Seasons + Participants importieren -------------------
        foreach ($showsArr as $idx => $s) {
            if (! is_array($s)) {
                Log::warning("Show-Datensatz #{$idx} ist kein Array und wird übersprungen.");
                continue;
            }

            $showName = Arr::get($s, 'name');
            if (! $showName) {
                Log::warning("Show-Datensatz #{$idx} ohne 'name' übersprungen.", ['data' => $s]);
                continue;
            }

            // Show-Slug ggf. generieren
            $showSlug = Arr::get($s, 'slug');
            if (! $showSlug || $showSlug === '') {
                $showSlug = $this->safeSlug($showName, 'show');
                Log::info("Show #{$idx}: slug fehlte – generiert '{$showSlug}' aus '{$showName}'.");
            } else {
                // normalize
                $showSlug = Str::slug($showSlug);
            }

            $show = Show::updateOrCreate(
                ['slug' => $showSlug],
                [
                    'name' => $showName,
                    'description' => Arr::get($s, 'description'),
                    'genre' => Arr::get($s, 'genre'),
                    'network' => Arr::get($s, 'network'),
                    'meta_title' => Arr::get($s, 'meta_title'),
                    'meta_description' => Arr::get($s, 'meta_description'),
                ]
            );

            // Seasons
            $seasons = Arr::get($s, 'seasons', []);
            if (! is_array($seasons)) {
                Log::warning("Show '{$show->name}' (#{$idx}) hat 'seasons' nicht als Array.");
                $seasons = [];
            }

            foreach ($seasons as $sIdx => $seasonData) {
                if (! is_array($seasonData)) continue;

                $seasonName = Arr::get($seasonData, 'name', 'Staffel');
                $seasonSlug = Arr::get($seasonData, 'slug'); // in deinem JSON fehlt der meist
                if (! $seasonSlug || $seasonSlug === '') {
                    // versuche aus name zu generieren, sonst aus id
                    $seasonSlug = $this->safeSlug(
                        $seasonName ?: (string)Arr::get($seasonData, 'id'),
                        'season'
                    );
                } else {
                    $seasonSlug = Str::slug($seasonSlug);
                }

                // Jahr aus premiere_date ableiten (falls vorhanden)
                $year = null;
                $premiere = Arr::get($seasonData, 'premiere_date');
                if ($premiere) {
                    try {
                        $year = Carbon::parse($premiere)->year;
                    } catch (\Throwable $e) {
                        Log::info("Konnte premiere_date nicht parsen: {$premiere}");
                    }
                }

                $season = Season::updateOrCreate(
                    ['show_id' => $show->id, 'slug' => $seasonSlug],
                    [
                        'name' => $seasonName,
                        'year' => $year,
                        'episode_count' => Arr::get($seasonData, 'episode_count'),
                        'meta_title' => Arr::get($seasonData, 'meta_title'),
                        'meta_description' => Arr::get($seasonData, 'meta_description'),
                    ]
                );

                // Participants
                $participants = Arr::get($seasonData, 'participants', []);
                if (! is_array($participants)) $participants = [];

                foreach ($participants as $pIdx => $p) {
                    if (! is_array($p)) continue;

                    // Kandidat ermitteln:
                    // 1) candidate_slug (falls vorhanden)
                    // 2) legacy candidate id (dein JSON nutzt "id")
                    // 3) Name → notfalls Kandidat anlegen
                    $resolvedCandidateId = null;

                    $candidateSlug = Arr::get($p, 'candidate_slug');
                    if ($candidateSlug) {
                        $cand = Candidate::where('slug', Str::slug($candidateSlug))->first();
                        if ($cand) $resolvedCandidateId = $cand->id;
                    }

                    if (!$resolvedCandidateId) {
                        $legacyId = $this->intOrNull(Arr::get($p, 'id'));
                        if ($legacyId !== null && isset($legacyIdToCandidateId[$legacyId])) {
                            $resolvedCandidateId = $legacyIdToCandidateId[$legacyId];
                        }
                    }

                    if (!$resolvedCandidateId) {
                        $name = Arr::get($p, 'name');
                        if ($name) {
                            // versuche per Name zu finden
                            $cand = Candidate::where('name', $name)->first();
                            if (! $cand) {
                                // anlegen (Slug generieren)
                                $slug = $this->safeSlug($name, 'cand');
                                $cand = Candidate::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                ]);
                            }
                            $resolvedCandidateId = $cand->id;
                        }
                    }

                    if (! $resolvedCandidateId) {
                        Log::warning("Teilnehmer ohne referenzierbaren Kandidaten übersprungen.", [
                            'show' => $show->name,
                            'season' => $season->name,
                            'participant' => $p
                        ]);
                        continue;
                    }

                    Participant::updateOrCreate(
                        ['season_id' => $season->id, 'candidate_id' => $resolvedCandidateId],
                        [
                            'role' => Arr::get($p, 'role'),
                            'placement' => Arr::get($p, 'placement'),
                            'prize_won' => Arr::get($p, 'prize_won'),
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ TrashConnectSeeder: Import abgeschlossen (fehlertolerant).');
    }
}
