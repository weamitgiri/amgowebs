@php $d = $data ?? []; @endphp
<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            @if (!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
            <h2>{{ $d['heading'] ?? '' }}</h2>
            @if (!empty($d['text']))<p>{{ $d['text'] }}</p>@endif
            @if (!empty($d['button_label']))
                <div class="cta-actions"><a href="{{ $d['button_url'] ?: url('/contact') }}" class="btn btn-primary btn-lg">{{ $d['button_label'] }} <span class="ico" data-icon="arrow"></span></a></div>
            @endif
        </div>
    </div>
</section>
