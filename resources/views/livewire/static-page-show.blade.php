<x-seo
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="$seo['canonical']"
/>
<nav aria-label="Breadcrumb" class="mb-4 text-sm">
    <ol class="flex gap-2">
        <li><a href="{{ route('home') }}" class="underline" wire:navigate>Start</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page" class="text-slate-600">{{ $page->title }}</li>
    </ol>
</nav>
<section class="tc-card tc-card-pad space-y-6">

    <h1 class="mb-4 text-3xl font-bold">{{ $page->title }}</h1>
    <article class="tc-rte">

        {!! $page->content !!} {{-- Admin-Inhalt, daher trusted --}}
    </article>
</section>
