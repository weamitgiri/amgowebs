@php $d = $data ?? []; $pkgs = $packages ?? collect(); @endphp
<section class="section">
    <div class="container">
        @if (!empty($d['heading']) || !empty($d['eyebrow']))
            <div class="section-head center" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        <div class="pricing-grid">
            @foreach ($pkgs as $package)
                @include('partials.site.package-card')
            @endforeach
        </div>
    </div>
</section>
