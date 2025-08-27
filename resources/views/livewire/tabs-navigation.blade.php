<x-seo
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="$seo['canonical']"
    :ogTitle="$seo['ogTitle']"
    :ogDescription="$seo['ogDescription']"
    :ogType="$seo['ogType']"
/>

<div x-data="{tab:'person'}" class="space-y-8">

    <section class="tc-card">
        <h1 class="sr-only">TrashConnect - Deine Trash-TV-Suchmaschine</h1>
        <div class="tc-card-pad space-y-6">
            <div class="tc-tabbar" role="tablist" aria-label="Suche">
                <button class="tc-tab" :class="tab==='person' ? 'tc-tab--active' : 'tc-tab--idle'"
                        @click="tab='person'" role="tab" :aria-selected="tab==='person'">Nach Personen suchen</button>
                <button class="tc-tab" :class="tab==='show' ? 'tc-tab--active' : 'tc-tab--idle'"
                        @click="tab='show'" role="tab" :aria-selected="tab==='show'">Nach Show suchen</button>
            </div>

            <div x-show="tab==='person'" role="tabpanel" class="space-y-6">
                @livewire('candidate-selector')
                <livewire:connection-finder :a-id="$aId" :b-id="$bId" :wire:key="'cf-'.$aId.'-'.$bId" />
            </div>

            <div x-show="tab==='show'" role="tabpanel">
                @livewire('show-browser')
            </div>
        </div>
    </section>
    {{-- Intro + Updates (2-spaltig) --}}
    <section class="grid gap-6 md:grid-cols-3">
        <div class="tc-card md:col-span-2">
            <div class="tc-card-pad space-y-4">
                <h2 class="text-xl font-semibold ">Updates und News</h2>
                <livewire:updates-feed :limit="5" />
            </div>
        </div>

        <aside class="tc-sidebar-card">
            <h2 class="text-xl font-semibold ">Buy me a coffee</h2>
            <p class="mt-4 text-base text-slate-600">
                TrashConnect ist privat finanziert und bleibt kostenlos.
                Wenn dir das Projekt gefällt, kannst du mich freiwillig auf <a class="tc-link" href="https://buymeacoffee.com/trashconnect">Buy Me a Coffee</a> unterstützen. Kein Abo, keine Paywall – einfach ein Danke. ☕️💜
            </p>
            <p class="mt-4 text-base text-slate-600">
                Folge auf <a href="https://www.instagram.com/trashconnect.de/" class="tc-link">Instagram</a> für mehr Updates!
            </p>
        </aside>
    </section>

    {{-- Tabs --}}

</div>
