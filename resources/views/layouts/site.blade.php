<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#05060f">
    @include('partials.site.meta')
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@400..800&display=swap" rel="stylesheet">
    @vite(['resources/css/site.css', 'resources/js/site.js'])
    <link rel="stylesheet" href="{{ asset('css/cms.css') }}?v=1">
    @stack('schema')
    @stack('head')
</head>
<body>
    @include('partials.site.header')

    <main>
        @yield('content')
    </main>

    @include('partials.site.floating')
    @include('partials.site.footer')
    @stack('scripts')
</body>
</html>
