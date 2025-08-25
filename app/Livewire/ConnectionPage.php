<?php

// app/Livewire/ConnectionPage.php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Season;
use App\Models\Show;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ConnectionPage extends Component
{
    public Candidate $a;
    public Candidate $b;

    public array $byShow = [];

    // 👇 Neu: Globale Anzeige-Helfer
    public bool $hasJoint = false;           // gibt es irgendwo eine gemeinsame Staffel?
    public array $sharedShows = [];          // alle Shows, in denen beide vorkommen (egal welche Staffel)

    public function mount(string $candA, string $candB): void
    {
        $a = Candidate::where('slug', $candA)->firstOrFail();
        $b = Candidate::where('slug', $candB)->firstOrFail();

        if ($a->id === $b->id) {
            abort(404);
        }
        if (strcmp($a->slug, $b->slug) > 0) {
            $this->redirectRoute('connections.show', ['candA' => $b->slug, 'candB' => $a->slug], navigate: true);
            return;
        }

        $this->a = $a;
        $this->b = $b;

        $seasonsA = Season::with('show')
            ->whereHas('participants', fn($q) => $q->where('candidate_id', $a->id))
            ->get();

        $seasonsB = Season::with('show')
            ->whereHas('participants', fn($q) => $q->where('candidate_id', $b->id))
            ->get();

        $groupA = $seasonsA->groupBy('show_id');
        $groupB = $seasonsB->groupBy('show_id');

        $sharedShowIds = $groupA->keys()->intersect($groupB->keys())->values();

        // Alle betroffenen Shows (für die Hinweiszeile)
        $shows = Show::whereIn('id', $sharedShowIds)->get()->keyBy('id');
        $this->sharedShows = $shows->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();

        $blocks = [];
        foreach ($sharedShowIds as $showId) {
            $show = $shows[$showId];

            $joint = Season::query()
                ->where('show_id', $showId)
                ->whereHas('participants', fn($q) => $q->where('candidate_id', $a->id))
                ->whereHas('participants', fn($q) => $q->where('candidate_id', $b->id))
                ->with(['participants.candidate'])
                ->get();

            $jointIds = $joint->pluck('id');
            $onlyA = $groupA[$showId]->whereNotIn('id', $jointIds)->values();
            $onlyB = $groupB[$showId]->whereNotIn('id', $jointIds)->values();

            $sortSeasons = fn($c) => $c
                ->sortBy(fn($s) => $s->year ?? PHP_INT_MAX)
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $blocks[] = [
                'show'   => $show,
                'joint'  => $sortSeasons($joint),
                'only_a' => $sortSeasons($onlyA),
                'only_b' => $sortSeasons($onlyB),
            ];
        }

        // Irgendwo gemeinsame Staffel?
        $this->hasJoint = collect($blocks)->contains(fn($b) => $b['joint']->isNotEmpty());

        usort($blocks, fn($x, $y) => strnatcasecmp($x['show']->name, $y['show']->name));
        $this->byShow = $blocks;
    }

    public function render()
    {
        $title = "Gemeinsame Auftritte von {$this->a->name} und {$this->b->name} - Trashconnect";

        $descJoint   = "Alle gemeinsamen Auftritte von {$this->a->name} und {$this->b->name}: Shows, Staffeln, komplette Teilnehmerlisten und Streaminglinks.";
        $descNoJoint = "Keine gemeinsame Staffel, aber gleiche Formate: Übersicht der Shows von {$this->a->name} und {$this->b->name} mit Staffeln, Besetzungen und Streaminglinks.";
        $description = \Illuminate\Support\Str::limit($this->hasJoint ? $descJoint : $descNoJoint, 160, '');

        return view('livewire.connection-page', [
            'seo' => [
                'title'       => $title,
                'description' => $description,
                'canonical'   => route('connections.show', ['candA' => $this->a->slug, 'candB' => $this->b->slug]),
                'ogTitle'     => $title,
                'ogDescription'=> $description,
                'ogType'      => 'website',
                // 'ogImage'   => asset('images/og-default.jpg'),
            ],
        ]);
    }
}
