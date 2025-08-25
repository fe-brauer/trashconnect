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

    #[Url(as: 'q')] public string $query = '';
    #[Url(as: 'genre')] public string $genre = '';

    public function updatingQuery(){ $this->resetPage(); }
    public function updatingGenre(){ $this->resetPage(); }

    public function render()
    {
        $shows = Show::when($this->query, fn($q)=>$q->where('name','like',"%{$this->query}%"))
            ->when($this->genre, fn($q)=>$q->where('genre',$this->genre))
            ->orderBy('name')->paginate(50);

        $genres = Show::whereNotNull('genre')->distinct()->pluck('genre')->filter()->values();

        return view('livewire.show-browser',
            [
                'shows' => $shows,
                'genres' => $genres,
                'seo' => [
                    'title'       => 'Shows – TrashConnect',
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
