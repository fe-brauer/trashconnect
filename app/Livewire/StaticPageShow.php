<?php

namespace App\Livewire;

use App\Models\StaticPage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StaticPageShow extends Component
{
    public StaticPage $page;

    public function mount(StaticPage $page): void
    {
        abort_unless($page->published, 404);
        $this->page = $page;
    }

    public function render()
    {
        // SEO -> via Stack-Komponente (x-seo)
        $title = ($this->page->meta_title ?: $this->page->title) . ' – TrashConnect';
        $desc  = $this->page->meta_description ?: str($this->page->content)->stripTags()->limit(160, '');

        return view('livewire.static-page-show', [
            'seo' => [
                'title'       => $title,
                'description' => $desc,
                'canonical'   => route('pages.show', $this->page->slug),
            ],
        ]);
    }
}
