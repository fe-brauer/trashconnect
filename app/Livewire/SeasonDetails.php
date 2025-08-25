<?php

namespace App\Livewire;

use App\Models\Season;
use App\Models\Show;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SeasonDetails extends Component
{
    public Show $show;
    public Season $season;

    public function mount(string $showSlug, string $seasonSlug)
    {
        $this->show = Show::where('slug',$showSlug)->firstOrFail();
        $this->season = Season::where('show_id',$this->show->id)
            ->where('slug',$seasonSlug)
            ->with('participants.candidate')
            ->firstOrFail();
    }

    public function render()
    {
        $title = "{$this->show->name} – {$this->season->name} – TrashConnect";
        $desc  = Str::limit(strip_tags($this->show->description ?? ''), 160, '…');

        return view('livewire.season-details')
            ->layout('components.layouts.app', [
                'title'       => $title,
                'description' => $desc,
                'canonical'   => url()->current(),
                'ogTitle'     => "{$this->show->name} – {$this->season->name}",
                'ogDescription'=> $desc,
                'ogType'      => 'video.tv_show',
                'ogImage'     => $this->show->cover_url ?? asset('images/og-default.jpg'),
            ]);
    }
}
