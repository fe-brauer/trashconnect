@php
    // Sicherstellen, dass alles da ist (verhindert N+1 in Blade)
    $candidate->loadMissing(
        'participants.season.show.network',
        'participants.season.participants.candidate'
    );

    // Social
    $sameAs = [];
    if (is_array($candidate->social_media)) {
        foreach ($candidate->social_media as $url) {
            if (!empty($url)) $sameAs[] = $url;
        }
    }

    // Auftritte (Participants) sortieren
    $parts = $candidate->participants->filter(fn($p) => $p->season && $p->season->show);
    $parts = $parts->sortBy([
        fn($a, $b) => ($a->season->year <=> $b->season->year)
            ?: strcmp($a->season->show->name, $b->season->show->name),
    ]);

    // Gruppierung: Shows -> Seasons
    $byShow = $parts->groupBy(fn($p) => $p->season->show->id)->map(function ($group) {
        $show = $group->first()->season->show;
        $seasons = $group->pluck('season')->unique('id')
            ->sortBy(fn($s) => [$s->year ?? 0, $s->name]);
        return (object) ['show' => $show, 'seasons' => $seasons];
    })->sortBy(fn($o) => $o->show->name)->values();

    // Stats
    $showsCount   = $byShow->count();
    $seasonsCount = $parts->pluck('season_id')->unique()->count();
    $firstYear    = $parts->pluck('season.year')->filter()->min();
    $lastYear     = $parts->pluck('season.year')->filter()->max();
    $networks     = $byShow->pluck('show.network')->filter()->unique('id')->values();

    // Top-Co-Appearances (mit wem am häufigsten gemeinsam in Staffeln)
    $co = collect();
    foreach ($parts as $p) {
        foreach ($p->season->participants as $op) {
            if ($op->candidate_id !== $candidate->id) $co->push($op->candidate);
        }
    }
    $topConnections = $co->groupBy('id')->map(function ($g) {
        return (object) ['candidate' => $g->first(), 'count' => $g->count()];
    })->sortByDesc('count')->take(8)->values();

    // knowsAbout für Schema
    $knowsAbout = $byShow->pluck('show.name')->all();

    // -------- JSON-LD --------
    $schemaPerson = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        'name'        => $candidate->name,
        'url'         => url()->current(),
        'image'       => $candidate->photo_url ?? null,
        'description' => $candidate->bio ? strip_tags($candidate->bio) : null,
        'birthDate'   => $candidate->birth_date?->toDateString(),
        'jobTitle'    => 'Reality-TV-Persönlichkeit',
        'sameAs'      => $sameAs ?: null,
        'knowsAbout'  => !empty($knowsAbout) ? $knowsAbout : null,
        'subjectOf'   => $byShow->map(fn($o) => [
            '@type' => 'TVSeries',
            'name'  => $o->show->name,
            'url'   => route('show.detail', ['id'=>$o->show->id,'slug'=>$o->show->slug]),
        ])->values()->all(),
    ], fn($v) => !is_null($v));

    $schemaBreadcrumbs = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>route('home')],
            ['@type'=>'ListItem','position'=>2,'name'=>'Kandidat:innen','item'=>route('candidates.index')],
            ['@type'=>'ListItem','position'=>3,'name'=>$candidate->name,'item'=>route('candidates.show',$candidate->slug)],
        ],
    ];
@endphp

@push('schema')
    <script type="application/ld+json">
        {!! json_encode($schemaPerson, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($schemaBreadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>
@endpush

<x-seo
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="$seo['canonical']"
    :ogTitle="$seo['ogTitle']"
    :ogDescription="$seo['ogDescription']"
    :ogType="$seo['ogType']"
/>

<div class="space-y-6">

    <nav aria-label="Breadcrumb" class="mb-4 text-sm">
        <ol class="flex gap-2">
            <li><a href="{{ route('home') }}" class="underline" wire:navigate>Start</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="text-slate-600">{{ $candidate->name }}</li>
        </ol>
    </nav>
    {{-- Kopf --}}
    <header>
        <h1 class="text-3xl font-extrabold tracking-tight text-tv-violet">{{ $candidate->name }}</h1>
        @if($candidate->bio)
            <p class="mt-3 max-w-3xl leading-relaxed text-slate-800">{{ $candidate->bio }}</p>
        @endif
    </header>

    <div class="grid gap-6 md:grid-cols-[320px_minmax(0,_1fr)]">
        {{-- Sidebar: Foto, Fakten, Social, Top-Connections --}}
        <aside class="rounded-2xl bg-white p-5 ring-1 ring-tv-border self-start space-y-6">
            @if($candidate->photo_url)
                <div class="flex justify-center">
                    <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}"
                         class="h-40 w-40 rounded-xl object-cover ring-1 ring-tv-border" loading="lazy" decoding="async">
                </div>
            @endif

            <section>
                <h2 class="mb-2 text-lg font-semibold text-tv-violet">Fakten</h2>
                <dl class="grid grid-cols-2 gap-x-3 gap-y-1 text-sm text-slate-800">
                    <dt class="opacity-70">Shows</dt><dd>{{ $showsCount }}</dd>
                    <dt class="opacity-70">Staffeln</dt><dd>{{ $seasonsCount }}</dd>
                    <dt class="opacity-70">Aktiv</dt><dd>
                        @if($firstYear && $lastYear) {{ $firstYear }}–{{ $lastYear }}
                        @elseif($firstYear) seit {{ $firstYear }}
                        @else — @endif
                    </dd>
                </dl>
            </section>

            @if($networks->isNotEmpty())
                <section>
                    <h3 class="mb-2 text-lg font-semibold text-tv-violet">Sender/Plattformen</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($networks as $n)
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-sm font-medium text-slate-700 ring-1 ring-tv-border/60">
                                @if($n?->logo_url)
                                    <img src="{{ $n->logo_url }}" alt="{{ $n->name }} Logo" class="mr-2 h-4 w-4 object-contain">
                                @endif
                                {{ $n->name ?? '—' }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(!empty($sameAs))
                <section>
                    <h3 class="mb-2 text-lg font-semibold text-tv-violet">Social</h3>
                    <div class="flex flex-wrap gap-2 text-sm">
                        @foreach($sameAs as $link)
                            <a href="{{ $link }}" target="_blank" rel="noopener nofollow"
                               class="rounded-full bg-slate-100 px-3 py-1 font-medium text-tv-violet ring-1 ring-tv-border/60 hover:bg-slate-200">
                                {{ parse_url($link, PHP_URL_HOST) ?? $link }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($topConnections->isNotEmpty())
                <section>
                    <h3 class="mb-2 text-lg font-semibold text-tv-violet">Häufig zusammen mit</h3>
                    <ul class="space-y-1 text-sm">
                        @foreach($topConnections as $tc)
                            <li>
                                <a href="{{ route('candidates.show', $tc->candidate->slug) }}" wire:navigate
                                   class="underline">{{ $tc->candidate->name }}</a>
                                <span class="ml-1 rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-tv-border/60">
                                    {{ $tc->count }}×
                                </span>
                                <a href="{{ route('connections.show', [$candidate->slug, $tc->candidate->slug]) }}" class="ml-2 text-xs underline">Verbindungen</a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>

        {{-- Main: Auftritte (gruppiert nach Show) --}}
        <section class="rounded-2xl bg-white p-5 ring-1 ring-tv-border">
            <h2 class="mb-4 text-xl font-semibold">Auftritte</h2>

            @if($byShow->isEmpty())
                <p>Keine Auftritte gefunden.</p>
            @else
                <div class="space-y-3">
                    @foreach($byShow as $row)
                        <article class="rounded-2xl bg-white shadow-sm ring-1 ring-tv-border px-4 py-3 hover:bg-slate-50">
                            <a class="text-tv-pink block font-medium hover:underline"
                               href="{{ route('show.detail', ['id'=>$row->show->id,'slug'=>$row->show->slug]) }}"
                               wire:navigate>
                                {{ $row->show->name }}
                            </a>
                            <ul class="mt-1 text-sm text-slate-600">
                                @foreach($row->seasons as $season)
                                    <li class="flex items-center gap-2">
                                        <span>·</span>
                                        <a class="underline"
                                           href="{{ route('season.show', [$row->show->slug, $season->slug]) }}"
                                           wire:navigate>
                                            {{ $season->name }}
                                        </a>
                                        @if($season->year)
                                            <span class="text-slate-500">({{ $season->year }})</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
