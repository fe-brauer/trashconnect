<?php

namespace App\Livewire;

use App\Models\Show;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ShowBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'q')]     public string $query = '';
    #[Url(as: 'genre')] public string $genre = '';

    public function render()
    {
        $shows = Show::when($this->query, fn ($q) => $q->where('name','like',"%{$this->query}%"))
            ->when($this->genre, fn ($q) => $q->where('genre',$this->genre))
            ->orderBy('name')
            ->paginate(50);

        return view('livewire.show-browser', [
            'shows'   => $shows,
        ]);
    }
}
