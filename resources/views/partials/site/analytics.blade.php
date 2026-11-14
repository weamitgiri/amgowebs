@php
    // Analytics / tag manager — IDs are managed in Admin → Settings → Analytics & Tracking.
    // Values are format-validated before being printed into a script, so an invalid
    // setting can never inject markup. GTM takes precedence (configure GA inside GTM).
    $gtm = trim((string) ($settings['google_tag_manager_id'] ?? ''));
    $ga = trim((string) ($settings['google_analytics_id'] ?? ''));
    $gtmOk = $gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/i', $gtm);
    $gaOk = $ga !== '' && preg_match('/^(G|UA|AW)-[A-Z0-9-]+$/i', $ga);
@endphp
@if ($gtmOk)
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
<!-- End Google Tag Manager -->
@elseif ($gaOk)
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $ga }}');</script>
@endif
