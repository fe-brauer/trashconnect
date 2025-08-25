@props(['blocks' => []])

<div class="tc-rte">
    @foreach($blocks as $block)
        @php($t = $block['type'] ?? null)
        @php($d = $block['data'] ?? [])
        @switch($t)
            @case('paragraph')
                <p>{{ $d['text'] ?? '' }}</p>
                @break

            @case('heading')
                @php($tag = in_array($d['level'] ?? 'h2', ['h2','h3','h4']) ? $d['level'] : 'h2')
                <<?= $tag ?>>{{ $d['text'] ?? '' }}</<?= $tag ?>>
@break

@case('list')
    @php($isOl = ($d['type'] ?? 'ul') === 'ol')
    @if(!empty($d['items']))
        @if($isOl)<ol>@else<ul>@endif
                @foreach($d['items'] as $it)
                    <li>{{ $it['text'] ?? '' }}</li>
            @endforeach
            @if($isOl)</ol>@else</ul>@endif
    @endif
    @break

@case('quote')
    <blockquote>
        <p>{{ $d['text'] ?? '' }}</p>
        @if(!empty($d['cite']))<footer class="mt-1 text-sm text-slate-600">— {{ $d['cite'] }}</footer>@endif
    </blockquote>
    @break

@case('image')
    @php($src = !empty($d['src']) ? (Str::startsWith($d['src'], ['http://','https://','/storage']) ? $d['src'] : Storage::disk('public')->url($d['src'])) : null)
    @if($src)
        <figure class="my-4">
            <img src="{{ $src }}" alt="{{ $d['alt'] ?? '' }}" class="rounded-xl">
            @if(!empty($d['caption']))<figcaption class="mt-2 text-sm text-slate-600">{{ $d['caption'] }}</figcaption>@endif
        </figure>
    @endif
    @break

@case('separator')
    <hr>
    @break

@case('callout')
    @php($tone = $d['tone'] ?? 'info')
    <div class="rounded-xl border p-4
        {{ match($tone) {
          'success' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
          'warning' => 'bg-amber-50 border-amber-200 text-amber-900',
          'danger'  => 'bg-rose-50 border-rose-200 text-rose-900',
          default   => 'bg-sky-50 border-sky-200 text-sky-900',
        } }}">
        @if(!empty($d['title']))<div class="font-semibold mb-1">{{ $d['title'] }}</div>@endif
        <div>{{ $d['body'] ?? '' }}</div>
    </div>
    @break
    @endswitch
    @endforeach
    </div>
