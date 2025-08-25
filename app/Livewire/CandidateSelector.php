<?php

namespace App\Livewire;

use App\Models\Candidate;
use Livewire\Component;

class CandidateSelector extends Component
{
    public string $qA = '';
    public string $qB = '';
    public $resA;
    public $resB;

    public function mount()
    {
        $this->resA = collect();
        $this->resB = collect();
    }

    public function updatedQA() { $this->resA = Candidate::where('name','like',"%{$this->qA}%")->limit(10)->get(); }
    public function updatedQB() { $this->resB = Candidate::where('name','like',"%{$this->qB}%")->limit(10)->get(); }

    public function selectA(int $id, string $name)
    {
        $this->dispatch('candidate-picked', slot: 'a', id: $id)->to(TabsNavigation::class);
        $this->qA = $name; $this->resA = collect();
    }

    public function selectB(int $id, string $name)
    {
        $this->dispatch('candidate-picked', slot: 'b', id: $id)->to(TabsNavigation::class);
        $this->qB = $name; $this->resB = collect();
    }

    public function render() { return view('livewire.candidate-selector'); }
}
