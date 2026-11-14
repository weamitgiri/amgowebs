@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Error 404</span>
        <h1 data-reveal data-delay="1">This page took a wrong turn</h1>
        <p data-reveal data-delay="2">The page you're looking for doesn't exist or may have moved. Let's get you back on track — try one of the links below, or head home.</p>
        <div class="cta-actions" data-reveal data-delay="3">
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg">Back to Home <span class="ico" data-icon="arrow"></span></a>
            <a href="{{ url('/contact') }}" class="btn btn-ghost btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">Popular Pages</span>
            <h2 class="section-title">Where would you like to go?</h2>
        </div>
        <div class="feature-grid">
            <a href="{{ url('/services') }}" class="feature feature--link" data-reveal data-delay="1">
                <div class="ic" data-icon="layout" data-size="26"></div>
                <h3>Our Services</h3>
                <p>Website design, e-commerce, redesigns, SEO and ongoing support.</p>
                <span class="feature-more">View services <span class="ico" data-icon="arrow"></span></span>
            </a>
            <a href="{{ url('/pricing') }}" class="feature feature--link" data-reveal data-delay="2">
                <div class="ic" data-icon="target" data-size="26"></div>
                <h3>Pricing</h3>
                <p>Transparent, fixed-price website packages starting from $199.</p>
                <span class="feature-more">See pricing <span class="ico" data-icon="arrow"></span></span>
            </a>
            <a href="{{ url('/blog') }}" class="feature feature--link" data-reveal data-delay="3">
                <div class="ic" data-icon="search" data-size="26"></div>
                <h3>Blog</h3>
                <p>Practical, jargon-free advice on web design, SEO and growing online.</p>
                <span class="feature-more">Read the blog <span class="ico" data-icon="arrow"></span></span>
            </a>
            <a href="{{ url('/contact') }}" class="feature feature--link" data-reveal data-delay="4">
                <div class="ic" data-icon="mail" data-size="26"></div>
                <h3>Get a Quote</h3>
                <p>Tell us about your project and get a free quote plus a review call.</p>
                <span class="feature-more">Get in touch <span class="ico" data-icon="arrow"></span></span>
            </a>
        </div>
    </div>
</section>
@endsection
