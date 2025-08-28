{{-- resources/views/livewire/show-details.blade.php (ersetzt die aktuelle Datei) --}}
@php
    // ---------- Helfer / Stats ----------
    $seasonsWithParticipants = $show->seasons->filter(fn($s) => $s->participants->isNotEmpty());
    $seasonCount   = $show->seasons->count();
    $firstYear     = $show->seasons->pluck('year')->filter()->min();
    $participantsTotal = $show->seasons->flatMap->participants->count();

    // „Bekannte Gesichter“ (Top 8 nach Häufigkeit innerhalb der Show)
    $notables = $show->seasons
        ->flatMap->participants
        ->groupBy('candidate_id')
        ->sortByDesc(fn($g) => $g->count())
        ->take(8)
        ->map(fn($g) => $g->first()->candidate)
        ->filter();

    // ---------- JSON-LD ----------
    $schemaTvSeries = [
        '@context' => 'https://schema.org',
        '@type'    => 'TVSeries',
        'name'     => $show->name,
        'inLanguage' => 'de',
        'description' => strip_tags($show->description ?? ''),
        'url'      => url()->current(),
        'image'    => $show->logo_url ?? ($show->cover_url ?? asset('images/og-default.jpg')),
        'genre'    => $show->genre ?: 'Reality-TV',
        'productionCompany' => $show->network?->name ? [
            '@type' => 'Organization',
            'name'  => $show->network->name,
            ...( $show->network->logo_url ? ['logo' => $show->network->logo_url] : [] ),
            ...( $show->streaming_url ? ['url' => $show->streaming_url] : [] ),
        ] : null,
        'sameAs' => array_values(array_filter([$show->social_url])),
        'containsSeason' => $show->seasons->map(function ($s) use ($show) {
            return [
                '@type' => 'TVSeason',
                'name'  => $s->name,
                'url'   => route('season.show', [$show->slug, $s->slug]),
                ...( $s->year ? ['datePublished' => (string)$s->year] : [] ),
                ...( isset($s->episode_count) ? ['numberOfEpisodes' => (int)$s->episode_count] : [] ),
            ];
        })->values()->all(),
    ];
    $schemaTvSeries = array_filter($schemaTvSeries, fn($v) => !is_null($v));

    $schemaBreadcrumbs = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>route('home')],
            ['@type'=>'ListItem','position'=>2,'name'=>'Shows','item'=>route('shows.index')],
            ['@type'=>'ListItem','position'=>3,'name'=>$show->name,'item'=>route('show.detail',['id'=>$show->id,'slug'=>$show->slug])],
        ],
    ];
@endphp

@push('schema')
    <script type="application/ld+json">
        {!! json_encode($schemaTvSeries, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($schemaBreadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>
@endpush

{{-- SEO aus der Komponente (bleibt wie gehabt) --}}
<x-seo
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="$seo['canonical']"
    :ogTitle="$seo['ogTitle']"
    :ogDescription="$seo['ogDescription']"
    :ogType="$seo['ogType']"
/>

<div>
    {{-- Breadcrumbs UI (optional) --}}
    <nav aria-label="Breadcrumb" class="mb-4 text-sm">
        <ol class="flex gap-2">
            <li><a href="{{ route('home') }}" class="underline" wire:navigate>Start</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('shows.index') }}" class="underline" wire:navigate>Shows</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="text-slate-600">{{ $show->name }}</li>
        </ol>
    </nav>

    <div class="grid gap-6 md:grid-cols-[350px_minmax(0,_1fr)]">
        {{-- SIDEBAR --}}
        <aside class="bg-tv-violet rounded-xl p-5 self-start space-y-6 ring-1 ring-tv-border">
            <div class="flex justify-center">
                @if($show->logo_url)
                    <img
                        src="{{ $show->logo_url }}"
                        alt="Logo {{ $show->name }}"
                        width="400" height="160"
                        class="h-auto w-2/3 max-w-[280px] object-contain"
                        loading="lazy" decoding="async">
                @else
                    <img
                        src="{{ asset('images/placeholder_logo.svg') }}"
                        alt="Logo Platzhalter"
                        width="160" height="160"
                        class="h-10 w-auto opacity-60"
                        loading="lazy" decoding="async">
                @endif
            </div>

            @if($show->description)
                <section>
                    <h2 class="mb-2 font-semibold text-white">Beschreibung</h2>
                    <p class="leading-relaxed text-white/95">{{ $show->description }}</p>
                    @if($show->network?->name)
                        <p class="mt-1 italic text-white/80">Quelle: {{ $show->network->name }}</p>
                    @endif
                </section>
            @endif

            @if($show->streaming_url || $show->network?->logo_url)
                <section>
                    <h2 class="mb-2 font-semibold text-white">Streaming</h2>
                    <div class="flex flex-wrap items-center gap-3">
                        @if($show->network?->logo_url)
                            <a href="{{ $show->streaming_url ?: '#' }}" @if($show->streaming_url) target="_blank" rel="noopener" @endif
                            class="inline-flex items-center rounded-md px-2 py-1 text-sm font-semibold text-white">
                                <img src="{{ $show->network->logo_url }}" alt="{{ $show->network->name }} Logo"
                                     width="120" height="40" class="mr-2 h-10 w-auto" loading="lazy" decoding="async">
                                {{ $show->network->name }}
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            <section>
                <h2 class="mb-2 font-semibold text-white">Fakten</h2>
                <dl class="grid grid-cols-2 gap-x-3 gap-y-1 text-white/95 text-sm">
                    <dt class="opacity-80">Genre</dt><dd>{{ $show->genre ?: 'Reality-TV' }}</dd>
                    <dt class="opacity-80">Sender</dt><dd>{{ $show->network->name ?? '—' }}</dd>
                    <dt class="opacity-80">Erstausstrahlung</dt><dd>{{ $firstYear ?: '—' }}</dd>
                    <dt class="opacity-80">Staffeln</dt><dd>{{ $seasonCount }}</dd>
                    <dt class="opacity-80">Teilnehmer:innen</dt><dd>{{ $participantsTotal }}</dd>
                </dl>
            </section>
        </aside>

        {{-- MAIN --}}
        <section class="bg-white p-5 rounded-xl ring-1 ring-tv-border">
            <header class="mb-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-tv-violet">{{ $show->name }}</h1>
                <p class="mt-2 text-slate-700">
                    Alle Staffeln mit Jahr und kompletter Besetzung. Auf Verbindungsseiten werden gesuchte Namen zusätzlich hervorgehoben.
                </p>
            </header>

            <div class="space-y-4">
                @forelse($seasonsWithParticipants as $season)
                    <article class="rounded-2xl bg-white shadow-sm ring-1 ring-tv-border">
                        <div class="flex items-center justify-between rounded-t-2xl bg-slate-200 px-4 py-2">
                            <div class="text-xl font-semibold">
                                <a href="{{ route('season.show', [$show->slug, $season->slug]) }}" wire:navigate
                                   class="hover:underline">{{ $season->name }}</a>
                            </div>
                            <div class="text-sm font-semibold text-slate-700">
                                @if($season->year) Jahr: {{ $season->year }} @endif
                            </div>
                        </div>

                        <div class="p-4 text-sm">
                            <ul class="flex flex-wrap gap-x-3 gap-y-2">
                                @foreach($season->participants as $p)
                                    <li>
                                        <a class="underline" href="{{ route('candidates.show', $p->candidate->slug) }}" wire:navigate>
                                            {{ $p->candidate->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @empty
                    <p class="text-slate-700">Noch keine Staffeln erfasst.</p>
                @endforelse
            </div>

            {{-- Redaktioneller Zusatz (optional – macht die Seite „contentiger“) --}}
            @if($show->long_summary ?? false)
                <section class="mt-8 rounded-2xl bg-slate-50 p-5 ring-1 ring-tv-border">
                    <h2 class="mb-2 text-2xl font-bold text-tv-violet">Über die Show</h2>
                    <div class="tc-rte">{{ $show->long_summary }}</div>
                </section>
            @endif
        </section>
    </div>
</div>
