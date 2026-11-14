@php $d = $data ?? []; $items = $d['items'] ?? []; @endphp
<section class="section">
    <div class="container">
        @if (!empty($d['heading']) || !empty($d['eyebrow']))
            <div class="section-head center" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow eyebrow--violet">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        <div class="feature-grid">
            @foreach ($items as $i => $item)
                @php
                    $href = trim($item['href'] ?? '');
                    $to = $href === '' ? null : (\Illuminate\Support\Str::startsWith($href, ['http://', 'https://', 'mailto:', 'tel:']) ? $href : url($href));
                @endphp
                @if ($to)
                    <a href="{{ $to }}" class="feature feature--link" data-reveal data-delay="{{ ($i % 4) + 1 }}">
                        <div class="ic" data-icon="{{ $item['icon'] ?? 'check' }}" data-size="26"></div>
                        <h3>{{ $item['title'] ?? '' }}</h3>
                        <p>{{ $item['text'] ?? '' }}</p>
                        <span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span>
                    </a>
                @else
                    <div class="feature" data-reveal data-delay="{{ ($i % 4) + 1 }}">
                        <div class="ic" data-icon="{{ $item['icon'] ?? 'check' }}" data-size="26"></div>
                        <h3>{{ $item['title'] ?? '' }}</h3>
                        <p>{{ $item['text'] ?? '' }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
