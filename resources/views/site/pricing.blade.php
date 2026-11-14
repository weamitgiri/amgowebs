@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Pricing &amp; Packages</span>
        <h1 data-reveal data-delay="1">Affordable website packages, <span class="text-grad">transparent pricing</span></h1>
        <p data-reveal data-delay="2">Fixed, one-time pricing with everything you need to launch — no monthly lock-in and no surprise fees. You own your website, code and content in full.</p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><span>Pricing</span></div>
    </div>
</section>
@include('partials.site.breadcrumb-schema', ['crumbs' => [
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'Pricing', 'url' => route('pricing')],
]])

<section class="section section--tight">
    <div class="container">
        <div class="pricing-grid">
            @foreach ($packages as $package)
                @include('partials.site.package-card')
            @endforeach
        </div>
    </div>
</section>

<!-- Comparison -->
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">Compare</span>
            <h2 class="section-title">Package comparison</h2>
        </div>
        <div class="compare-wrap" data-reveal>
            <table class="compare">
                <thead>
                    <tr>
                        <th>Feature</th>
                        @foreach ($packages as $package)<th>{{ $package->name }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Price</td>@foreach ($packages as $package)<td>{{ $package->price_label }}@unless($package->is_custom_pricing) <span style="color:var(--muted-2);font-size:12px">one-time</span>@endunless</td>@endforeach</tr>
                    <tr><td>Pages included</td>@foreach ($packages as $package)<td>{{ $package->pages_included ? 'Up to '.$package->pages_included : '—' }}</td>@endforeach</tr>
                    <tr><td>Delivery time</td>@foreach ($packages as $package)<td>{{ $package->delivery_days ? $package->delivery_days.' days' : 'Custom' }}</td>@endforeach</tr>
                    <tr><td>Free support</td>@foreach ($packages as $package)<td>{{ $package->support_months ? $package->support_months.' months' : '—' }}</td>@endforeach</tr>
                    <tr><td>Revisions</td>@foreach ($packages as $package)<td>{{ $package->revisions ?? 'Custom' }}</td>@endforeach</tr>
                    <tr><td>Responsive design</td>@foreach ($packages as $package)<td class="ok">✓</td>@endforeach</tr>
                    <tr><td>On-page SEO</td>@foreach ($packages as $package)<td class="ok">✓</td>@endforeach</tr>
                    <tr>
                        <td></td>
                        @foreach ($packages as $package)
                            <td><a href="{{ route('contact', ['package' => $package->slug]) }}" class="btn btn-outline" style="padding:9px 16px;font-size:13px">{{ $package->cta_label }}</a></td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="center" style="margin-top:20px;color:var(--muted-2);font-size:13.5px">Prices are one-time and in USD. You keep full ownership of your website and files — host it anywhere you like, and we'll help you set it up. Need something custom? <a href="{{ url('/contact') }}" style="color:var(--cyan)">Request a quote</a>.</p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            <span class="eyebrow">Not sure which to pick?</span>
            <h2>Tell us your goals — we'll recommend the right package</h2>
            <p>Submit your requirements and get a free quote plus a 15-minute review meeting.</p>
            <div class="cta-actions"><a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Get Your Website <span class="ico" data-icon="arrow"></span></a></div>
        </div>
    </div>
</section>
@endsection
