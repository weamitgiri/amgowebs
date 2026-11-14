@php
    /** @var \App\Models\Package $package */
    $featured = $package->is_featured;
    $included = $package->relationLoaded('includedFeatures') && $package->includedFeatures->isNotEmpty()
        ? $package->includedFeatures
        : $package->features->where('type', 'included');
@endphp
<div @class(['price-card', 'price-card--featured' => $featured]) data-reveal>
    @if ($featured)<span class="price-badge">Most Popular</span>@endif
    <div class="price-ic" data-icon="{{ $package->icon ?: 'layout' }}" data-size="24"></div>
    <h3>{{ $package->name }}</h3>
    <p class="price-desc">{{ $package->short_description }}</p>
    <div class="price-amount">
        @if ($package->is_custom_pricing)
            <span class="price-num">Custom</span>
        @else
            @if ($package->has_discount)<span class="price-old">${{ number_format((float) $package->original_price, 0) }}</span>@endif
            <span class="price-num">${{ number_format((float) $package->price_usd, 0) }}</span>
            <span class="price-unit">one-time</span>
        @endif
    </div>
    <ul class="price-features">
        @foreach ($included as $feature)
            <li><span class="pf-ck" data-icon="check" data-size="14"></span> {{ $feature->label }}</li>
        @endforeach
    </ul>
    <a href="{{ route('contact', ['package' => $package->slug]) }}" class="btn {{ $featured ? 'btn-primary' : 'btn-outline' }} btn-block">
        {{ $package->cta_label }} <span class="ico" data-icon="arrow" data-size="18"></span>
    </a>
    <a href="{{ route('packages.show', $package->slug) }}" class="price-details">View details</a>
</div>
