@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Blog &amp; Insights</span>
        <h1 data-reveal data-delay="1">{!! $heading !!}</h1>
        <p data-reveal data-delay="2">Practical tips on web design, SEO, and growing your business online.</p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><span>Blog</span></div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        @if ($categories->isNotEmpty())
            <div class="flex-cta" style="justify-content:center;margin-bottom:36px" data-reveal>
                <a href="{{ route('blog.index') }}" class="btn btn-outline" style="padding:9px 18px;font-size:13.5px">All</a>
                @foreach ($categories as $category)
                    <a href="{{ route('blog.category', $category->slug) }}" class="btn btn-outline" style="padding:9px 18px;font-size:13.5px">{{ $category->name }}</a>
                @endforeach
            </div>
        @endif

        @if ($posts->isEmpty())
            <p class="center" style="color:var(--muted-2)">No articles published yet. Check back soon.</p>
        @else
            <div class="blog-grid">
                @foreach ($posts as $post)
                    @include('partials.site.post-card')
                @endforeach
            </div>
            <div style="margin-top:40px">{{ $posts->links() }}</div>
        @endif
    </div>
</section>
@endsection
