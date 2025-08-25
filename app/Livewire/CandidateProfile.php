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
        $title = $this->candidate->name.' – Kandidat:innenprofil - TrashConnect';
        $desc  = Str::limit(strip_tags($this->candidate->bio ?? ''), 160, '…');

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
