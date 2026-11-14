@php
    $d = $data ?? [];
    $bg = trim((string) ($d['background_image'] ?? ''));
    // Only accept a plain URL/path; strip anything that could break out of the CSS url().
    $bg = preg_match('~^(https?://|/)[^\s\'"()<>]+$~i', $bg) ? $bg : '';
    $heroStyle = 'min-height:auto;padding-top:160px'
        .($bg ? ";background-image:linear-gradient(rgba(5,6,15,.74),rgba(5,6,15,.88)),url('{$bg}');background-size:cover;background-position:center" : '');
@endphp
<section class="hero{{ $bg ? ' hero--image' : '' }}" style="{!! $heroStyle !!}">
    <div class="hero-grid"></div>
    <div class="container">
        <div class="hero-copy" style="max-width:760px;margin-inline:auto;text-align:center">
            @if (!empty($d['eyebrow']))<span class="eyebrow" data-reveal>{{ $d['eyebrow'] }}</span>@endif
            @if (!empty($d['heading']))<h1 data-reveal data-delay="1" style="margin-inline:auto">{!! nl2br(e($d['heading'])) !!}@if (!empty($d['highlight'])) <span class="text-grad">{{ $d['highlight'] }}</span>@endif</h1>@endif
            @if (!empty($d['subheading']))<p class="lead" data-reveal data-delay="2" style="margin-inline:auto">{{ $d['subheading'] }}</p>@endif
            <div class="hero-actions" data-reveal data-delay="3" style="justify-content:center">
                @if (!empty($d['primary_label']))<a href="{{ $d['primary_url'] ?: '#' }}" class="btn btn-primary btn-lg">{{ $d['primary_label'] }} <span class="ico" data-icon="arrow"></span></a>@endif
                @if (!empty($d['secondary_label']))<a href="{{ $d['secondary_url'] ?: '#' }}" class="btn btn-ghost btn-lg">{{ $d['secondary_label'] }}</a>@endif
            </div>
        </div>
    </div>
</section>
