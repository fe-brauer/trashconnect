<div>
    <h1 class="mb-6 text-3xl font-bold">Kandidat:innen</h1>
    <input class="mb-6 w-full max-w-md rounded bg-tv-border p-2" placeholder="Suchen…" wire:model.live.debounce.300ms="query">

    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($candidates as $c)
            <li class="tc-card">
                <a class="text-tv-pink font-medium" wire:navigate href="{{ route('candidates.show', $c->slug) }}">{{ $c->name }}</a>
            </li>
        @endforeach
    </ul>
    <div class="mt-6">{{ $candidates->links() }}</div>
</div>
