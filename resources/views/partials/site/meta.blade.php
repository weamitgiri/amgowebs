@php
    $siteName = $settings['site_name'] ?? config('app.name');
    $t = $seo['title'] ?? ($settings['seo_default_title'] ?? $siteName);
    $d = $seo['description'] ?? ($settings['seo_default_description'] ?? '');
    $canonical = $seo['canonical'] ?? url()->current();
    $robots = $seo['robots'] ?? ($settings['seo_robots_default'] ?? 'index, follow');
    $ogTitle = $seo['og_title'] ?? $t;
    $ogDesc = $seo['og_description'] ?? $d;
    $ogImage = $seo['og_image'] ?? ($settings['seo_default_og_image'] ?? '');
    $ogType = $seo['og_type'] ?? 'website';

    // Social profile URLs for Organization sameAs (skip empty / placeholder "#").
    $sameAs = array_values(array_filter([
        $settings['social_facebook'] ?? null,
        $settings['social_instagram'] ?? null,
        $settings['social_linkedin'] ?? null,
        $settings['social_x'] ?? null,
        $settings['social_youtube'] ?? null,
    ], fn ($u) => $u && $u !== '#'));

    $organization = array_filter([
        '@type' => 'Organization',
        '@id' => url('/') . '#organization',
        'name' => $settings['seo_organization_name'] ?? $siteName,
        'url' => url('/'),
        'logo' => !empty($settings['logo']) ? $settings['logo'] : null,
        'description' => $settings['seo_default_description'] ?? null,
        'email' => $settings['contact_email'] ?? null,
        'telephone' => (!empty($settings['contact_phone']) && $settings['contact_phone'] !== '+1 (000) 000-0000') ? $settings['contact_phone'] : null,
        'areaServed' => 'Worldwide',
        'sameAs' => $sameAs ?: null,
    ], fn ($v) => !is_null($v) && $v !== []);

    $website = [
        '@type' => 'WebSite',
        '@id' => url('/') . '#website',
        'name' => $siteName,
        'url' => url('/'),
        'publisher' => ['@id' => url('/') . '#organization'],
        'inLanguage' => str_replace('_', '-', app()->getLocale()),
    ];
@endphp
<title>{{ $t }}</title>
<meta name="description" content="{{ $d }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $canonical }}">
<meta name="robots" content="{{ $robots }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDesc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
@if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDesc }}">
@if ($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif

{{-- Organization + WebSite structured data (site-wide) --}}
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@graph' => [$organization, $website]], JSON_UNESCAPED_SLASHES) !!}
</script>
@if (!empty($seo['schema']))
<script type="application/ld+json">{!! json_encode($seo['schema'], JSON_UNESCAPED_SLASHES) !!}</script>
@endif
