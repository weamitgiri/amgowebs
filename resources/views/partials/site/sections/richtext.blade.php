@php
    $d = $data ?? [];
    $narrow = ($d['width'] ?? 'normal') === 'narrow';
    $dark = ($d['tone'] ?? 'light') === 'dark';
@endphp
<section class="section {{ $dark ? 'section--dark' : '' }}">
    <div class="container" style="{{ $narrow ? 'max-width:820px' : '' }}">
        @if (!empty($d['eyebrow']) || !empty($d['heading']))
            <div class="section-head {{ $narrow ? '' : 'center' }}" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow eyebrow--violet">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        @if (!empty($d['body']))
            <div class="prose" data-reveal style="{{ $narrow ? '' : 'max-width:860px;margin-inline:auto' }}">
                {!! $d['body'] !!}
            </div>
        @endif
    </div>
</section>
