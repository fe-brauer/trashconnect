@php
    // Sicherstellen, dass alles da ist (verhindert N+1 in Blade)
    $candidate->loadMissing(
        'participants.season.show.network',
        'participants.season.participants.candidate'
    );

    // Social
    $sameAs = [];
    if (!empty($candidate->instagram_url)) {
        $sameAs[] = $candidate->instagram_url;
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

                @if(!empty($candidate->instagram_url))
                    <section>
                        <h3 class="mb-2 text-lg font-semibold text-tv-violet">Social Media</h3>
                        <a href="{{ $candidate->instagram_url }}" target="_blank" rel="noopener nofollow"
                           class="text-tv-violet hover:text-tv-pink flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="fill-current h-8 w-8" aria-hidden="true">
                                <path d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z"/>
                            </svg>
                        </a>
                    </section>
                @endif

            @if($topConnections->isNotEmpty())
                <section>
                    <h3 class="mb-2 text-lg font-semibold text-tv-violet">Häufig zusammen mit</h3>
                    <ul class="space-y-1 text-sm">
                        @foreach($topConnections as $tc)
                            <li>
                                <a href="{{ route('candidates.show', $tc->candidate->slug) }}" wire:navigate
                                   class="underline hover:text-tv-pink">{{ $tc->candidate->name }}</a>
                                <span class="ml-1 rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-tv-border/60">
                                    {{ $tc->count }}×
                                </span>
                                <a href="{{ route('connections.show', [$candidate->slug, $tc->candidate->slug]) }}" class="hover:text-tv-pink ml-2 text-xs underline">Verbindungen</a>
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
