<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Show;
use Livewire\Component;

class ConnectionFinder extends Component
{
    public ?int $aId = null;
    public ?int $bId = null;

    public ?Candidate $a = null;
    public ?Candidate $b = null;

    /** @var \App\Models\Show[] */
    public array $sharedShows = [];

    public function mount(?int $aId = null, ?int $bId = null): void
    {
        $this->aId = $aId;
        $this->bId = $bId;
        $this->loadData();
    }

    public function updated($field): void
    {
        if (in_array($field, ['aId','bId'], true)) {
            $this->loadData();
        }
    }


    protected function loadData(): void
    {
        $this->a = $this->aId ? Candidate::with('participants.season.show')->find($this->aId) : null;
        $this->b = $this->bId ? Candidate::with('participants.season.show')->find($this->bId) : null;

        $this->sharedShows = [];

        if (! $this->a || ! $this->b) {
            return;
        }
        if ($this->a->id === $this->b->id) {
            // gleiche Person – keine Weiterleitung, klare Anzeige darunter:
            return;
        }

        $idsA = $this->a->participants->pluck('season.show.id')->filter()->unique();
        $idsB = $this->b->participants->pluck('season.show.id')->filter()->unique();
        $shared = $idsA->intersect($idsB)->values();

        if ($shared->isNotEmpty()) {
            // ✅ Treffer: per wire:navigate auf die Kanonical-URL
            $this->redirectRoute(
                'connections.show',
                ['candA' => $this->a->slug, 'candB' => $this->b->slug],
                navigate: true
            );
            return;
        }

        // ❌ Keine Treffer: direkt unter den Feldern anzeigen
        $this->sharedShows = Show::whereIn('id', $shared)->orderBy('name')->get()->all();
    }

    public function render()
    {
        return view('livewire.connection-finder');
    }
}
