<x-seo :title="$seo['title']" :description="$seo['description']" :canonical="$seo['canonical']" />


<section class="tc-card tc-card-pad space-y-6">
    <h1 class="mb-4 text-3xl font-bold">{{ $update->title }}</h1>
    <article class="tc-rte">
        {!! $update->content !!}
    </article>
</section>


