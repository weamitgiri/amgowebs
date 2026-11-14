@php $d = $data ?? []; $items = $d['items'] ?? []; @endphp
<section class="section section--dark">
    <div class="container">
        @if (!empty($d['heading']) || !empty($d['eyebrow']))
            <div class="section-head center" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        <div class="stats-grid">
            @foreach ($items as $i => $item)
                <div class="stat" data-reveal data-delay="{{ ($i % 4) + 1 }}">
                    <div class="num">{{ $item['value'] ?? '' }}<span class="plus">{{ $item['suffix'] ?? '' }}</span></div>
                    <h4>{{ $item['label'] ?? '' }}</h4>
                </div>
            @endforeach
        </div>
    </div>
</section>
