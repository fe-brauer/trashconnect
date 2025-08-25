<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TabsNavigation extends Component
{
    public ?int $aId = null;
    public ?int $bId = null;

    #[On('candidate-picked')]
    public function setCandidate(string $slot, int $id): void
    {
        if ($slot === 'a') $this->aId = $id;
        if ($slot === 'b') $this->bId = $id;
    }

    public function render()
    {
        return view('livewire.tabs-navigation',
            [
                'seo' => [
                    'title'       => 'TrashConnect - Deine Reality-TV-Suchmaschine',
                    'description' => 'Alle Reality-TV-Shows im Überblick – filterbar und durchsuchbar.',
                    'canonical'   => url()->current(),
                    'ogTitle'     => 'Shows',
                    'ogDescription'=> 'Alle Reality-TV-Shows im Überblick – filterbar und durchsuchbar.',
                    'ogType'      => 'website',
                ]
            ]
        );
    }
}
