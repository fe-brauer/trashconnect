{{-- resources/views/livewire/connection-page.blade.php --}}
<x-seo
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="$seo['canonical']"
    :ogTitle="$seo['ogTitle']"
    :ogDescription="$seo['ogDescription']"
    :ogType="$seo['ogType']"
/>
<div class="space-y-8">
    <header class="flex flex-wrap items-center gap-3">
        <h1 class="text-2xl font-bold text-tv-violet">
            Verbindungen: {{ $a->name }} & {{ $b->name }}
        </h1>
    </header>

    @if(empty($byShow))
        <p class="text-slate-700">Keine gemeinsamen Shows/Staffeln gefunden.</p>
    @else
        {{-- 👇 Hinweiszeile: keine gemeinsame Staffel insgesamt --}}
        @if(!$hasJoint)
            <div class="tc-card">
                <div class="tc-card-pad space-y-3">
                    <p class="text-slate-800">
                        <strong>Hinweis:</strong> {{ $a->name }} und {{ $b->name }} waren <strong>nicht in derselben Staffel</strong> zu sehen.
                    </p>
                    <p class="text-slate-700">
                        Beide waren jedoch Kandidat:in in diesen Formaten:
                    </p>
                </div>
            </div>
        @endif

        {{-- Deine bestehende Ausgabe pro Show (inkl. joint/onlyA/onlyB) bleibt darunter unverändert --}}
        <div class="space-y-6">
            @foreach($byShow as $block)
                @php
                    $show   = $block['show'];
                    $joint  = $block['joint'];
                    $onlyA  = $block['only_a'];
                    $onlyB  = $block['only_b'];
                @endphp

                <section class="tc-card">
                    <div class="space-y-4">
                        {{-- Show Kopf --}}
                        <div class="flex items-center justify-between gap-4 bg-tv-violet rounded-t-2xl px-4 py-2">
                            <div class="flex items-center gap-3">
                                @if($show->logo_url ?? false)
                                    <img src="{{ $show->logo_url }}" alt="Logo {{ $show->name }}" class="h-8 w-auto" loading="lazy" decoding="async">
                                @endif
                                <a class="text-white text-lg" href="{{ route('show.detail', ['id'=>$show->id,'slug'=>$show->slug]) }}" wire:navigate>
                                    {{ $show->name }}
                                </a>
                            </div>
                        </div>

                        {{-- Gemeinsame Staffeln (mit Teilnehmer:innen) --}}
                        @if($joint->isNotEmpty())
                            <div class="space-y-4 p-4">
                                @foreach($joint as $season)
                                    <div class="rounded-2xl ring-1 ring-tv-border">
                                        <div class="rounded-t-2xl bg-slate-200 px-4 py-2 flex items-center justify-between">
                                            <div class="font-semibold text-lg">{{ $season->name }}</div>
                                            <div class="text-sm font-medium text-slate-700">
                                                @if($season->year) Jahr: {{ $season->year }} @endif
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            @php
                                                // A/B nach vorne sortieren
                                                $participants = $season->participants->sortBy(function($x) use ($a,$b) {
                                                  return in_array($x->candidate_id, [$a->id, $b->id]) ? 0 : 1;
                                                })->values();
                                            @endphp
                                            <ul class="flex flex-wrap gap-2">
                                                @foreach($participants as $p)
                                                    @php
                                                        $isA = $p->candidate_id === $a->id;
                                                        $isB = $p->candidate_id === $b->id;
                                                    @endphp
                                                    <li>
                                                        <a href="{{ route('candidates.show', $p->candidate->slug) }}"
                                                           wire:navigate
                                                           class="inline-flex items-center rounded-full px-3 py-1 text-sm ring-1 transition
                                 {{ ($isA || $isB)
                                      ? 'bg-tv-violet/10 text-tv-violet ring-tv-violet/20 font-semibold'
                                      : 'bg-white text-slate-700 ring-tv-border hover:bg-slate-50' }}">
                                                            {{ $p->candidate->name }}
                                                            @if($isA)
                                                                <span class="ml-2 tc-badge">A</span>
                                                            @elseif($isB)
                                                                <span class="ml-2 tc-badge">B</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Keine gemeinsame Staffel? Zeige je Show die Staffeln von A und B --}}
                        @if($joint->isEmpty() && ($onlyA->isNotEmpty() || $onlyB->isNotEmpty()))
                            <div class="grid gap-4 md:grid-cols-2 p-4">
                                <div class="rounded-xl ring-1 ring-tv-border p-4">
                                    <div class="mb-2 font-semibold">Staffeln mit {{ $a->name }}</div>
                                    <ul class="flex flex-wrap gap-2">
                                        @foreach($onlyA as $s)
                                            <li>
                                                <a class="tc-badge"
                                                   href="{{ route('season.show', ['showSlug' => $show->slug, 'seasonSlug' => $s->slug]) }}"
                                                   wire:navigate>
                                                    {{ $s->name }}@if($s->year), {{ $s->year }}@endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="rounded-xl ring-1 ring-tv-border p-4">
                                    <div class="mb-2 font-semibold">Staffeln mit {{ $b->name }}</div>
                                    <ul class="flex flex-wrap gap-2">
                                        @foreach($onlyB as $s)
                                            <li>
                                                <a class="tc-badge"
                                                   href="{{ route('season.show', ['showSlug' => $show->slug, 'seasonSlug' => $s->slug]) }}"
                                                   wire:navigate>
                                                    {{ $s->name }}@if($s->year), {{ $s->year }}@endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>




