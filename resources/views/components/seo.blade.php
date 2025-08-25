@props([
  'title' => 'TrashConnect',
  'description' => 'Finde Verbindungen zwischen Reality-TV-Persönlichkeiten',
  'canonical' => null,
  'ogTitle' => null,
  'ogDescription' => null,
  'ogType' => 'website',
  'ogImage' => null,
])

@push('head')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if($canonical)<link rel="canonical" href="{{ $canonical }}">@endif

    <meta property="og:title" content="{{ $ogTitle ?? $title }}">
    <meta property="og:description" content="{{ $ogDescription ?? $description }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
@endpush
