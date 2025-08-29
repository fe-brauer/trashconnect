<div>
    <nav aria-label="Breadcrumb" class="mb-4 text-sm">
        <ol class="flex gap-2">
            <li><a href="{{ route('home') }}" class="underline" wire:navigate>Start</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="text-slate-600">Alle Shows</li>
        </ol>
    </nav>

    <section class="tc-card tc-card-pad space-y-6">
    <h1 class="text-3xl font-extrabold tracking-tight text-tv-violet">Alle Shows</h1>
    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($shows as $show)
            <li class="bg-tv-violet rounded-xl shadow-sm ring-1 ring-tv-border  text-center p-2">
                <a class="flex flex-col gap-6 items-center justify-center text-white font-semibold" href="{{ route('show.detail',['id'=>$show->id,'slug'=>$show->slug]) }}" wire:navigate>
                    {{ $show->name }}
                    @if($show->logo_url)
                        <img
                            src="{{ $show->logo_url }}"
                            alt="Logo {{ $show->name }}"
                            width="400" height="400"
                            class="h-auto w-2/3 object-contain max-h-[75px]"
                            loading="lazy"
                            decoding="async"
                        >
                    @endif
                </a>

            </li>
        @empty
            <li>Keine Shows gefunden.</li>
        @endforelse
    </ul>

    <div class="mt-6">{{ $shows->links() }}</div>

    </section>
</div>
