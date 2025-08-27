<?php

namespace App\Livewire;

use App\Models\Candidate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CandidateProfile extends Component
{
    public Candidate $candidate;

    public function mount(string $slug)
    {
        $this->candidate = Candidate::where('slug', $slug)
            ->with('participants.season.show')
            ->firstOrFail();
    }

    public function render()
    {
        // Shows laden (über Seasons der Teilnahmen)
        $this->candidate->loadMissing('participants.season.show');

        $shows = $this->candidate->participants
            ->pluck('season.show.name')
            ->filter()
            ->unique()
            ->sort()            // optional: stabil/ABC
            ->values();

        $showList = $shows->join(', ');

        // Beschreibung im gewünschten Format + auf ~160 Zeichen begrenzen
        $desc = Str::limit(
            "{$this->candidate->name} - Teilgenommen bei: {$showList}",
            160,
            '…'
        );

        $title = $this->candidate->name.' – Kandidat:innenprofil - TrashConnect';

        return view('livewire.candidate-profile', [
            'seo' => [
                'title'       => $title,
                'description' => $desc,
                'canonical'   => url()->current(),
                'ogTitle'     => $this->candidate->name,
                'ogDescription'=> $desc,
                'ogType'      => 'profile',
                'ogImage'     => $this->candidate->photo_url ?? asset('images/og-default.jpg'),
            ]
        ]);
    }
}
