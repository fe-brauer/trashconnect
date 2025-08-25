<x-seo :title="$seo['title']" :description="$seo['description']" :canonical="$seo['canonical']" />

<section class="tc-card tc-card-pad">
    <article class="tc-rte">
        {!! $update->content !!}   {{-- 1:1 HTML-Rendern --}}
    </article>
</section>


