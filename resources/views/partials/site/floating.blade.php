@php
    // Telegram chat shortcut. Accepts a full https://t.me/... URL, or a bare
    // @username / username / phone — set it in Admin → Settings → Contact → Telegram.
    $tg = trim((string) ($settings['telegram'] ?? ''));
    $tgUrl = $tg === '' ? null
        : (\Illuminate\Support\Str::startsWith($tg, ['http://', 'https://']) ? $tg : 'https://t.me/'.ltrim($tg, '@/'));
@endphp
@if ($tgUrl)
<a class="tg-float" href="{{ $tgUrl }}" target="_blank" rel="noopener" aria-label="Chat on Telegram">
    <span data-icon="telegram" data-size="26"></span>
</a>
@endif
<a class="callback-float" href="{{ url('/contact') }}" aria-label="Get your website">
    <span class="cf-ic" data-icon="phone" data-size="20"></span>
    <span><b>Get Your Website <span class="sla">FAST</span></b><small>Free quote &amp; 15-min review</small></span>
</a>
