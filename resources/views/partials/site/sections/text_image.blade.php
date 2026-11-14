@php $d = $data ?? []; $left = ($d['align'] ?? 'right') === 'left'; @endphp
<section class="section">
    <div class="container">
        <div class="about-split">
            <div data-reveal class="{{ $left ? 'order-2' : '' }}" style="{{ $left ? 'order:2' : '' }}">
                @if (!empty($d['eyebrow']))<span class="eyebrow eyebrow--violet">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
                @if (!empty($d['body']))<div class="section-sub" style="margin-top:16px">{!! $d['body'] !!}</div>@endif
            </div>
            <div class="about-visual" data-reveal data-delay="1" style="{{ $left ? 'order:1' : '' }}">
                @if (!empty($d['image_url']))
                    <img src="{{ $d['image_url'] }}" alt="{{ $d['heading'] ?? '' }}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-xl)">
                @else
                    <div class="glow-ball"></div>
                @endif
            </div>
        </div>
    </div>
</section>
