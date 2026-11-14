@extends('layouts.site')

@section('content')
<section class="page-hero" style="padding-bottom:30px">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>{{ $package->price_label }}@unless($package->is_custom_pricing) one-time @endunless</span>
        <h1 data-reveal data-delay="1">{{ $package->name }}</h1>
        <p data-reveal data-delay="2">{{ $package->short_description }}</p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><a href="{{ url('/pricing') }}">Pricing</a><span class="sep">/</span><span>{{ $package->name }}</span></div>
        @include('partials.site.breadcrumb-schema', ['crumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Pricing', 'url' => route('pricing')],
            ['name' => $package->name, 'url' => route('packages.show', $package->slug)],
        ]])
        <div class="flex-cta" style="justify-content:center;margin-top:26px" data-reveal data-delay="3">
            <a href="{{ route('contact', ['package' => $package->slug]) }}" class="btn btn-primary btn-lg">{{ $package->cta_label }} <span class="ico" data-icon="arrow"></span></a>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="about-split">
            <div data-reveal>
                <span class="eyebrow eyebrow--violet">What's included</span>
                <h2 class="section-title">Everything in {{ $package->name }}</h2>
                @if ($package->description)<p class="section-sub">{{ $package->description }}</p>@endif
                <ul class="check-list" style="margin-top:22px">
                    @foreach ($package->features->where('type', 'included') as $f)
                        <li><span class="ck" data-icon="check" data-size="16"></span> {{ $f->label }}</li>
                    @endforeach
                </ul>

                @php $excluded = $package->features->where('type', 'excluded'); @endphp
                @if ($excluded->isNotEmpty())
                    <h3 style="font-size:16px;margin:24px 0 10px;color:var(--muted)">Not included</h3>
                    <ul class="check-list">
                        @foreach ($excluded as $f)
                            <li style="color:var(--muted-2)"><span class="ck" style="background:rgba(255,255,255,.04);color:var(--muted-2)">–</span> {{ $f->label }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div data-reveal data-delay="1">
                <div class="price-card price-card--featured" style="position:sticky;top:100px">
                    <div class="price-ic" data-icon="{{ $package->icon ?: 'layout' }}" data-size="24"></div>
                    <h3>{{ $package->name }}</h3>
                    <div class="price-amount">
                        @if ($package->is_custom_pricing)
                            <span class="price-num">Custom</span>
                        @else
                            @if ($package->has_discount)<span class="price-old">${{ number_format((float) $package->original_price, 0) }}</span>@endif
                            <span class="price-num">${{ number_format((float) $package->price_usd, 0) }}</span><span class="price-unit">one-time</span>
                        @endif
                    </div>
                    <ul class="price-features">
                        @if ($package->pages_included)<li><span class="pf-ck" data-icon="check" data-size="14"></span> Up to {{ $package->pages_included }} pages</li>@endif
                        @if ($package->delivery_days)<li><span class="pf-ck" data-icon="check" data-size="14"></span> {{ $package->delivery_days }}-day delivery</li>@endif
                        @if ($package->support_months)<li><span class="pf-ck" data-icon="check" data-size="14"></span> {{ $package->support_months }} months support</li>@endif
                        @if ($package->revisions)<li><span class="pf-ck" data-icon="check" data-size="14"></span> {{ $package->revisions }} revisions</li>@endif
                    </ul>
                    <a href="{{ route('contact', ['package' => $package->slug]) }}" class="btn btn-primary btn-block">{{ $package->cta_label }} <span class="ico" data-icon="arrow" data-size="18"></span></a>

                    @php $addons = $package->features->where('type', 'addon'); @endphp
                    @if ($addons->isNotEmpty())
                        <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--line)">
                            <p style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--muted-2);margin-bottom:10px">Optional add-ons</p>
                            @foreach ($addons as $f)
                                <div style="display:flex;justify-content:space-between;font-size:13.5px;padding:5px 0;color:var(--text)"><span>{{ $f->label }}</span><span style="color:var(--cyan)">+${{ number_format((float) $f->addon_price, 0) }}</span></div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if ($related->isNotEmpty())
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal><span class="eyebrow">Other packages</span><h2 class="section-title">Compare alternatives</h2></div>
        <div class="pricing-grid" style="grid-template-columns:repeat({{ min(3, $related->count()) }},1fr)">
            @foreach ($related as $package)
                @include('partials.site.package-card')
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
