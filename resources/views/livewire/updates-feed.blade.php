<div class="space-y-3">
    @forelse($items as $u)
        <article class="flex items-start gap-3">
            <time class="shrink-0 text-base text-slate-500">
                {{ ($u->published_at ?? $u->created_at)->format('d.m.Y') }}
            </time>
            <div class="min-w-0 pl-3 border-l border-l-slate-500">
                <a href="{{ route('updates.detail', $u->slug) }}" class="inline-block text-tv-pink font-semibold hover:underline" wire:navigate>
                    {{ $u->title }}
                </a>
                <p>{{ $u->excerpt }}</p>
            </div>
        </article>
    @empty
        <p class="text-slate-600">Noch keine Updates.</p>
    @endforelse
</div>

