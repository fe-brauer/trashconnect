<div class="space-y-3">
    @if(!$aId || !$bId)
        <p class="text-slate-700">Wähle zwei Kandidat:innen, um Verbindungen zu entdecken.</p>
    @elseif(empty($sharedShows))
        <h2 class="text-xl font-semibold text-slate-800">
            Gemeinsame Shows: {{ $a->name }} & {{ $b->name }}
        </h2>
        <p class="text-slate-700">Keine gemeinsamen Shows gefunden.</p>
    @else
        <h2 class="text-xl font-semibold text-slate-800">
            Gemeinsame Shows: {{ $a->name }} & {{ $b->name }}
        </h2>
        <ul class="tc-list">
            @foreach($sharedShows as $show)
                <li class="">
                    <a class="tc-show-link" href="{{ route('show.detail',['id'=>$show->id,'slug'=>$show->slug]) }}">
                        {{ $show->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
