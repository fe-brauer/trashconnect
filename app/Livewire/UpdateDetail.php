<?php

namespace App\Livewire;

use App\Models\Update;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class UpdateDetail extends Component
{
    public Update $update;

    public function mount(string $slug): void
    {
        $this->update = Update::with(['shows','tags'])
            ->where('slug',$slug)
            ->when(app()->isProduction(), fn($q)=>$q->published())
            ->firstOrFail();
    }

    public function render()
    {
        $title = ($this->update->meta_title ?: $this->update->title) . ' – Updates – TrashConnect';
        $desc  = $this->update->meta_description
            ?: \Illuminate\Support\Str::limit(strip_tags($this->update->excerpt ?: $this->update->content), 160, '');

        return view('livewire.update-detail', [
            'seo' => [
                'title'       => $title,
                'description' => $desc,
                'canonical'   => route('updates.detail', $this->update->slug),
            ],
        ]);
    }
}

