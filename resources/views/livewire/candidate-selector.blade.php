<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="candA" class="mb-2 block text-slate-700">Kandidat:in A</label>
        <input id="candA" class="tc-field" placeholder="Namen eingeben…" wire:model.live="qA" aria-controls="candA-list">
        @if($resA->isNotEmpty())
            <ul id="candA-list" class="tc-select-list" role="listbox">
                @foreach($resA as $r)
                    <li role="option">
                        <button type="button" class="tc-list-item" wire:click="selectA({{ $r->id }}, @js($r->name))">
                            {{ $r->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div>
        <label for="candB" class="mb-2 block text-slate-700">Kandidat:in B</label>
        <input id="candB" class="tc-field" placeholder="Namen eingeben…" wire:model.live="qB" aria-controls="candB-list">
        @if($resB->isNotEmpty())
            <ul id="candB-list" class="tc-select-list" role="listbox">
                @foreach($resB as $r)
                    <li role="option">
                        <button type="button" class="tc-list-item" wire:click="selectB({{ $r->id }}, @js($r->name))">
                            {{ $r->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
