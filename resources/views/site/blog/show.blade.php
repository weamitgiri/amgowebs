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

@if ($related->isNotEmpty())
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal><span class="eyebrow">Keep reading</span><h2 class="section-title">Related articles</h2></div>
        <div class="blog-grid">
            @foreach ($related as $post)
                <a class="blog-card" href="{{ route('blog.show', $post->slug) }}" data-reveal>
                    <div class="thumb"><span class="cat">{{ $post->category?->name ?? 'Article' }}</span><span data-icon="pen" data-size="40" style="color:var(--cyan);opacity:.4"></span></div>
                    <div class="bc-body"><div class="meta">{{ $post->published_at?->format('M j, Y') }}</div><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p><span class="read">Read <span data-icon="arrow" data-size="15"></span></span></div>
                </a>
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
