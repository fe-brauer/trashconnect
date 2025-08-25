<?php

namespace App\Livewire;

use App\Models\Update;
use Livewire\Component;

class UpdatesFeed extends Component
{
    public int $limit = 10;
    public ?string $tag = null;   // tag-slug optional
    public ?int $showId = null;   // show-id optional

    public function render()
    {
        $q = Update::published()
            ->with(['tags:id,name,slug,color', 'shows:id,name,slug'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at');

        if ($this->tag) {
            $q->whereHas('tags', fn($qq)=>$qq->where('slug', $this->tag));
        }
        if ($this->showId) {
            $q->whereHas('shows', fn($qq)=>$qq->where('id', $this->showId));
        }

        return view('livewire.updates-feed', [
            'items' => $q->limit($this->limit)->get(),
        ]);
    }
}

