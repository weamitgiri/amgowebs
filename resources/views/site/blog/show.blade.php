@extends('layouts.site')

@section('content')
<section class="page-hero" style="padding-bottom:20px">
    <div class="hero-grid"></div>
    <div class="container" style="max-width:800px">
        <span class="eyebrow" data-reveal>{{ $post->category?->name ?? 'Article' }}</span>
        <h1 data-reveal data-delay="1">{{ $post->title }}</h1>
        <div class="breadcrumb" data-reveal data-delay="2">
            <a href="{{ url('/') }}">Home</a><span class="sep">/</span><a href="{{ route('blog.index') }}">Blog</a><span class="sep">/</span><span>{{ \Illuminate\Support\Str::limit($post->title, 40) }}</span>
        </div>
        @include('partials.site.breadcrumb-schema', ['crumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
        ]])
        <p data-reveal data-delay="2" style="margin-top:16px;color:var(--muted-2);font-size:14px">
            By {{ $post->author?->name ?? 'Team' }} · {{ $post->published_at?->format('M j, Y') }} · {{ $post->reading_time }} min read
        </p>
    </div>
</section>

@if ($post->banner_image)
<div class="container" style="max-width:880px;margin-top:8px">
    <img src="{{ $post->banner_image }}" alt="{{ $post->image_alt_text }}" width="1600" height="900" fetchpriority="high" class="post-banner" data-reveal>
</div>
@endif

<section class="section section--tight">
    <div class="container" style="max-width:800px">
        <div class="prose" style="margin-inline:auto" data-reveal>
            {!! $post->body !!}
        </div>

        @if ($post->tags->isNotEmpty())
            <div class="flex-cta" style="margin-top:30px">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="btn btn-outline" style="padding:7px 14px;font-size:12.5px">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@php $postFaqs = $post->visibleFaqs; @endphp
@if ($postFaqs->isNotEmpty())
<section class="section section--tight">
    <div class="container" style="max-width:800px">
        <div class="section-head" data-reveal style="text-align:left;margin-bottom:24px"><span class="eyebrow eyebrow--violet">FAQ</span><h2 class="section-title">Frequently asked questions</h2></div>
        <div class="faq-list" data-reveal data-delay="1">
            @foreach ($postFaqs as $faq)
                <div class="faq-item"><button class="faq-q" type="button">{{ $faq->question }} <span class="pm" data-icon="plus" data-size="16"></span></button><div class="faq-a"><p>{{ $faq->answer }}</p></div></div>
            @endforeach
        </div>
    </div>
</section>
@push('schema')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $postFaqs->map(fn ($f) => ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags((string) $f->answer)]])->values()->all()], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif

@if ($related->isNotEmpty())
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal><span class="eyebrow">Keep reading</span><h2 class="section-title">Related articles</h2></div>
        <div class="blog-grid">
            @foreach ($related as $post)
                @include('partials.site.post-card')
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            <span class="eyebrow">Ready to start?</span>
            <h2>Get your professional website today</h2>
            <div class="cta-actions"><a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Get Your Website <span class="ico" data-icon="arrow"></span></a></div>
        </div>
    </div>
</section>
@endsection
