<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ \App\Models\Setting::get('site_name', config('app.name')) }} Admin</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased" x-data="{ sidebar: false }">
<div class="min-h-full lg:flex">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-300 transition-transform lg:static lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center gap-2 px-6 border-b border-white/10">
            <span class="h-3 w-3 rounded-full" style="background:linear-gradient(120deg,#6c5cff,#00e5ff,#ff3d9a)"></span>
            <span class="font-bold text-white text-lg">{{ \App\Models\Setting::get('site_name', 'YourBrand') }}</span>
        </div>
        <nav class="px-3 py-4 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 4rem)">
            @php
                $nav = [
                    ['Dashboard', 'admin.dashboard', 'dashboard.view', 'grid'],
                    ['Inquiries', 'admin.inquiries.index', 'inquiries.view', 'inbox'],
                    ['Packages', 'admin.packages.index', 'packages.view', 'box'],
                    ['Pages', 'admin.pages.index', 'pages.view', 'file'],
                    ['Blog', 'admin.posts.index', 'blog.view', 'pen'],
                    ['Categories', 'admin.categories.index', 'blog.view', 'folder'],
                    ['Tags', 'admin.tags.index', 'blog.view', 'tag'],
                    ['Careers', 'admin.jobs.index', 'jobs.view', 'briefcase'],
                    ['Media', 'admin.media.index', 'media.view', 'image'],
                    ['Navigation', 'admin.menus.index', 'menus.manage', 'menu'],
                    ['SEO Manager', 'admin.seo.index', 'seo.view', 'search'],
                    ['Settings', 'admin.settings.edit', 'settings.manage', 'cog'],
                    ['Users', 'admin.users.index', 'users.view', 'users'],
                    ['Roles', 'admin.roles.index', 'roles.manage', 'shield'],
                    ['Activity Log', 'admin.activity.index', 'activity.view', 'activity'],
                ];
            @endphp
            @foreach ($nav as [$label, $routeName, $perm, $icon])
                @permission($perm)
                    @php
                        $href = \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : null;
                        $active = $href && request()->url() === $href;
                        $soon = is_null($href);
                    @endphp
                    <a href="{{ $href ?? '#' }}"
                       @class([
                           'flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                           'bg-white/10 text-white' => $active,
                           'hover:bg-white/5 hover:text-white' => ! $active,
                           'opacity-50 cursor-default pointer-events-none' => $soon,
                       ])>
                        <span>{{ $label }}</span>
                        @if ($soon)<span class="text-[10px] uppercase tracking-wide text-slate-500">soon</span>@endif
                    </a>
                @endpermission
            @endforeach
        </nav>
    </aside>

    <div x-show="sidebar" @click="sidebar=false" class="fixed inset-0 z-30 bg-black/40 lg:hidden" style="display:none"></div>

    {{-- Main --}}
    <div class="flex min-h-full flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 lg:px-8">
            <div class="flex items-center gap-3">
                <button @click="sidebar=!sidebar" class="lg:hidden rounded-md p-2 hover:bg-slate-100" aria-label="Toggle menu">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4" x-data="{ open: false }">
                <a href="{{ url('/') }}" target="_blank" class="hidden sm:inline text-sm text-slate-500 hover:text-slate-800">View site ↗</a>
                <div class="relative">
                    <button @click="open=!open" class="flex items-center gap-2 rounded-full bg-slate-100 py-1.5 pl-1.5 pr-3 text-sm hover:bg-slate-200">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-slate-900 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden sm:inline font-medium text-slate-700">{{ auth()->user()->name }}</span>
                    </button>
                    <div x-show="open" @click.outside="open=false" style="display:none"
                         class="absolute right-0 mt-2 w-52 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->role?->name }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
