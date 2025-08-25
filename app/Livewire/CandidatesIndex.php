<?php

namespace App\Livewire;

use App\Models\Candidate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CandidatesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')] public string $query = '';

    public function updatingQuery(){ $this->resetPage(); }

    public function render()
    {
        $candidates = Candidate::when($this->query, fn($q)=>$q->where('name','like',"%{$this->query}%"))
            ->orderBy('name')->paginate(30);

        return view('livewire.candidates-index', compact('candidates'))
            ->layout('components.layouts.app', [
                'title'       => 'Kandidat:innen – TrashConnect',
                'description' => 'Entdecke alle Kandidat:innen aus deutschen Reality-TV-Shows.',
                'canonical'   => url()->current(),
                'ogTitle'     => 'Kandidat:innen',
                'ogDescription'=> 'Entdecke alle Kandidat:innen aus deutschen Reality-TV-Shows.',
                'ogType'      => 'website',
            ]);
    }
}
