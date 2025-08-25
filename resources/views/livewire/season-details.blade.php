@php
    // Teilnehmer als Actor-Liste (nur Name+URL, kompakt)
    $actors = $season->participants->map(function($p) {
        return [
            '@type' => 'Person',
            'name'  => $p->candidate->name,
            'url'   => route('candidates.show', $p->candidate->slug),
        ];
    })->values()->all();

    $schemaTvSeason = array_filter([
        '@context' => 'https://schema.org',
        '@type'    => 'TVSeason',
        'name'     => $season->name,
        'url'      => url()->current(),
        'image'    => $show->cover_url ?? null, // falls vorhanden
        'datePublished'    => $season->year ? "{$season->year}-01-01" : null, // grober Jahresanker
        'numberOfEpisodes' => $season->episode_count ?: null,
        'partOfSeries' => [
            '@type' => 'TVSeries',
            'name'  => $show->name,
            'url'   => route('show.detail', ['id'=>$show->id,'slug'=>$show->slug]),
        ],
        'actor' => $actors,
    ], fn($v) => !is_null($v));

    $schemaBreadcrumbs = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>route('home')],
            ['@type'=>'ListItem','position'=>2,'name'=>'Shows','item'=>route('shows.index')],
            ['@type'=>'ListItem','position'=>3,'name'=>$show->name,'item'=>route('show.detail',['id'=>$show->id,'slug'=>$show->slug])],
            ['@type'=>'ListItem','position'=>4,'name'=>$season->name,'item'=>route('season.show',[$show->slug,$season->slug])],
        ],
    ];
@endphp

@push('schema')
    <script type="application/ld+json">
        @json($schemaTvSeason, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
    <script type="application/ld+json">
        @json($schemaBreadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
@endpush


<div>
    <h1 class="text-3xl font-bold">{{ $show->name }} · {{ $season->name }}</h1>

    <section class="mt-8">
        <h2 class="mb-3 text-xl font-semibold">Teilnehmende</h2>
        @forelse($season->participants as $p)
            <div class="tc-card mb-3">
                <a class="text-tv-pink font-medium" href="{{ route('candidates.show', $p->candidate->slug) }}">
                    {{ $p->candidate->name }}
                </a>
                <div class="text-sm text-tv-border">
                    Rolle: {{ $p->role ?? '—' }} | Platzierung: {{ $p->placement ?? '—' }}
                </div>
            </div>
        @empty
            <p>Noch keine Teilnehmenden erfasst.</p>
        @endforelse
    </section>
</div>
