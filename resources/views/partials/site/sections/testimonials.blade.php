@php
    $d = $data ?? [];
    // Only render genuine testimonials (a filled quote). An empty section stays
    // invisible — so the slot is admin-ready but we never ship fabricated reviews.
    $items = array_values(array_filter($d['items'] ?? [], fn ($i) => filled($i['quote'] ?? null)));
@endphp
@if (!empty($items))
<section class="section section--dark">
    <div class="container">
        @if (!empty($d['heading']) || !empty($d['eyebrow']))
            <div class="section-head center" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        <div class="testi-grid">
            @foreach ($items as $i => $item)
                <div class="testi" data-reveal data-delay="{{ ($i % 3) + 1 }}">
                    <div class="stars">★★★★★</div>
                    <p>"{{ $item['quote'] ?? '' }}"</p>
                    <div class="who"><span class="av">{{ strtoupper(substr($item['name'] ?? 'A', 0, 1)) }}</span><span><b>{{ $item['name'] ?? '' }}</b><span>{{ $item['role'] ?? '' }}</span></span></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
