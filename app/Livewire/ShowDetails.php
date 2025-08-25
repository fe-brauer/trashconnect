<?php

namespace App\Livewire;

use App\Models\Show;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ShowDetails extends Component
{
    public Show $show;

    public function mount(int $id, string $slug)
    {
        $this->show = \App\Models\Show::with([
            'seasons' => function ($q) {
                $q->whereHas('participants')
                    // Numerisch nach der Zahl hinter "staffel-"
                    ->orderByRaw("
              CASE
                WHEN slug LIKE 'staffel-%' THEN CAST(SUBSTR(slug, 9) AS INTEGER)
                WHEN name LIKE 'Staffel %' THEN CAST(SUBSTR(name, 9) AS INTEGER)
                ELSE 999999
              END ASC
          ")
                    ->orderBy('name') // Fallback bei gleichen Zahlen
                    ->with(['participants.candidate']);
            },
        ])->findOrFail($id);

        if ($this->show->slug !== $slug) {
            redirect()->route('show.detail', ['id'=>$this->show->id, 'slug'=>$this->show->slug], 301)->send();
        }
    }

    public function render()
    {
        $title = $this->show->name.' – Showdetails - TrashConnect';
        $desc  = Str::limit(strip_tags($this->show->description ?? ''), 150, '…');

        return view('livewire.show-details', [
            'seo' => [
                'title'       => $title,
                'description' => $desc,
                'canonical'   => url()->current(),
                'ogTitle'     => $this->show->name,
                'ogDescription'=> $desc,
                'ogType'      => 'video.tv_show',
                'ogImage'     => $this->show->cover_url ?? asset('images/og-default.jpg'),
            ]
        ]);
    }
}
