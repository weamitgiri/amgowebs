@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>{{ $page->title }}</span>
        <h1 data-reveal data-delay="1">{{ $page->title }}</h1>
        @if ($page->excerpt)<p data-reveal data-delay="2">{{ $page->excerpt }}</p>@endif
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><span>{{ $page->title }}</span></div>
    </div>
</section>
@include('partials.site.breadcrumb-schema', ['crumbs' => [
    ['name' => 'Home', 'url' => url('/')],
    ['name' => $page->title, 'url' => url('/'.$page->slug)],
]])

@if ($page->sections->where('is_visible', true)->isNotEmpty())
    {{-- Page-builder sections --}}
    @foreach ($page->sections->where('is_visible', true) as $section)
        @include('partials.site.sections.' . $section->type, [
            'data' => $section->data ?? [],
            'packages' => $packages ?? collect(),
            'posts' => $posts ?? collect(),
        ])
    @endforeach
@elseif (!empty($packages) && $packages->isNotEmpty())
    {{-- Services page fallback: pricing teaser --}}
    <section class="section section--tight">
        <div class="container">
            <div class="section-head center" data-reveal><span class="eyebrow eyebrow--violet">Packages</span><h2 class="section-title">Affordable website packages</h2></div>
            <div class="pricing-grid">
                @foreach ($packages as $package)
                    @include('partials.site.package-card')
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            <span class="eyebrow">Get Started</span>
            <h2>Launch your professional website</h2>
            <p>Submit your requirements and get a free quote plus a 15-minute review meeting.</p>
            <div class="cta-actions"><a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Get Your Website <span class="ico" data-icon="arrow"></span></a></div>
        </div>
    </div>
</section>
@endsection
