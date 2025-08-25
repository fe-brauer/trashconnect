<div class="space-y-4">
    <div class="grid gap-3">
        <label class="block">
            <span class="mb-1 block text-sm text-tv-border">Meta Title</span>
            <input class="w-full rounded bg-tv-border p-2" wire:model.live="title" maxlength="70">
        </label>

        <label class="block">
            <span class="mb-1 block text-sm text-tv-border">Meta Description</span>
            <textarea class="w-full rounded bg-tv-border p-2" rows="3" wire:model.live="description" maxlength="160"></textarea>
        </label>

        <label class="block">
            <span class="mb-1 block text-sm text-tv-border">Keywords (kommasepariert)</span>
            <input class="w-full rounded bg-tv-border p-2" wire:model.live="keywords">
        </label>

        <label class="block">
            <span class="mb-1 block text-sm text-tv-border">Schema.org JSON-LD</span>
            <textarea class="w-full font-mono rounded bg-tv-border p-2" rows="8" wire:model.live="schema_markup" placeholder='{"@context":"https://schema.org","@type":"TVSeries","name":"…"}'></textarea>
        </label>
    </div>

    <button class="tc-btn" wire:click="save">Speichern</button>
</div>
