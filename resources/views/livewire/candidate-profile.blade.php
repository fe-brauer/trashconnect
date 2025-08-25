@php
    // sameAs aus social_media (falls vorhanden)
    $sameAs = [];
    if (is_array($candidate->social_media)) {
        foreach ($candidate->social_media as $url) {
            if (!empty($url)) $sameAs[] = $url;
        }
    }

    // optionales "knowsAbout" aus Shows, in denen die Person auftrat
    $knowsAbout = $candidate->participants
        ->map(fn($p) => optional($p->season->show)->name)
        ->filter()
        ->unique()
        ->values()
        ->all();

    $schemaPerson = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        'name'        => $candidate->name,
        'url'         => url()->current(),
        'image'       => $candidate->photo_url ?? null, // falls du ein Feld hast
        'description' => $candidate->bio ? strip_tags($candidate->bio) : null,
        'birthDate'   => $candidate->birth_date?->toDateString(),
        'jobTitle'    => 'Reality-TV-Persönlichkeit',
        'sameAs'      => $sameAs ?: null,
        'knowsAbout'  => !empty($knowsAbout) ? $knowsAbout : null,
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
        @json($schemaPerson, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
    <script type="application/ld+json">
        @json($schemaBreadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </script>
@endpush


<div>
    <h1 class="text-3xl font-bold">{{ $candidate->name }}</h1>

    @if($candidate->bio)
        <p class="mt-4 max-w-3xl leading-relaxed text-neutral-200">{{ $candidate->bio }}</p>
    @endif

    <section class="mt-8">
        <h2 class="mb-3 text-xl font-semibold">Auftritte</h2>
        <div class="space-y-4">
        @forelse($candidate->participants as $p)

                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-tv-border px-4 py-2 group group:hover:bg-slate-200">
                    <div class="font-medium">
                        <a class="text-tv-pink block" wire:navigate href="{{ route('show.detail',['id'=>$p->season->show->id,'slug'=>$p->season->show->slug]) }}">
                            {{ $p->season->show->name }}
                        </a>
                        <span class="text-slate-500">· {{ $p->season->name }}</span>
                    </div>
                </div>

        @empty
            <p>Keine Auftritte gefunden.</p>
        @endforelse
        </div>
    </section>
</div>
