@php
    $schemaTvSeries = [
        '@context' => 'https://schema.org',
        '@type'    => 'TVSeries',
        'name'     => $show->name,
        'description' => strip_tags($show->description ?? ''),
        'url'      => url()->current(),
        'image'    => $show->logo_url ?? ($show->cover_url ?? asset('images/og-default.jpg')),
        'genre'    => $show->genre ?: 'Reality-TV',
        'productionCompany' => array_filter([
            '@type' => 'Organization',
            'name'  => $show->network ?? null,
        ], fn($v) => !is_null($v)),
        // verlinke Staffeln (leichtgewichtige Darstellung)
        'containsSeason' => $show->seasons->map(function ($s) use ($show) {
            return [
                '@type' => 'TVSeason',
                'name'  => $s->name,
                'url'   => route('season.show', [$show->slug, $s->slug]),
            ];
        })->values()->all(),
    ];

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
        @json($schemaTvSeries, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
    <script type="application/ld+json">
        @json($schemaBreadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
@endpush

<div>


    <div class="md:grid-cols-[350px_minmax(0,_1fr)] grid gap-6">
        <div class="bg-tv-violet rounded-xl p-4 self-start space-y-6">
            <div class="flex justify-center">
            @if($show->logo_url)

                    <img
                        src="{{ $show->logo_url }}"
                        alt="Logo {{ $show->name }}"
                        width="400" height="400"
                        class="h-auto w-2/3 object-contain max-w-[275px]"
                        loading="lazy"
                        decoding="async"
                    >
                    @else
                        <img
                            src="{{ asset('images/placeholder_logo.svg') }}"
                            alt="Logo Platzhalter"
                            width="160" height="160"
                            class="h-10 w-auto opacity-60"
                            loading="lazy"
                            decoding="async"
                        >

            @endif
            </div>

            @if($show->description)
                <div class="">
                    <span class="block font-semibold text-white mb-2">Beschreibung:</span>
                    <p class="max-w-3xl leading-relaxed text-white">{{ $show->description }}</p>
                    <p class="italic text-white">Quelle: {{ $show->network->name }}</p>
                </div>
            @endif

            @if($show->network)
                <div>
                    <span class="block font-semibold text-white mb-2">Streaming:</span>
                    <div class="flex items-center gap-3">
                        @if($show->network->logo_url)
                            <a href="{{ $show->streaming_url }}" target="_blank" rel="noopener">
                                <img src="{{ $show->network->logo_url }}" alt="{{ $show->network->name }} Logo"
                                     width="120" height="60" class="h-8 w-auto" loading="lazy" decoding="async">
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="bg-white p-4 rounded-xl">
            <section class="">
                <div class="space-y-8">
                    <h1 class="text-3xl font-bold text-tv-violet mb-10">{{ $show->name }}</h1>
                    @forelse($show->seasons as $season)
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-tv-border">
                            <div class="bg-slate-200 rounded-t-2xl px-4 py-2 flex items-center justify-between">
                                <div class="font-semibold text-xl">{{ $season->name }}</div>
                                <div class="text-base font-semibold ">
                                    @if($season->year) Jahr: {{ $season->year }} @endif
                                </div>
                            </div>

                            <div class="text-sm p-4">
                                <ul class="flex flex-wrap gap-2">
                                    @foreach($season->participants as $p)
                                        <li>
                                            <a class="underline" href="{{ route('candidates.show', $p->candidate->slug) }}" wire:navigate>
                                                {{ $p->candidate->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @empty
                        <p>Noch keine Staffeln erfasst.</p>
                    @endforelse
                </div>
            </section>
        </div>

    </div>


</div>
