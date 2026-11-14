<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · {{ \App\Models\Setting::get('site_name', config('app.name')) }}</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 text-slate-200 antialiased">
<div class="flex min-h-full items-center justify-center p-6"
     style="background:radial-gradient(1000px 600px at 70% -10%, rgba(108,92,255,.25), transparent 60%), radial-gradient(800px 500px at 10% 110%, rgba(0,229,255,.15), transparent 55%), #0b0e1f">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 text-2xl font-bold text-white">
                <span class="h-3.5 w-3.5 rounded-full" style="background:linear-gradient(120deg,#6c5cff,#00e5ff,#ff3d9a)"></span>
                {{ \App\Models\Setting::get('site_name', 'YourBrand') }}
            </div>
            <p class="mt-2 text-sm text-slate-400">Admin Panel — sign in to continue</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur">
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-4 py-3 text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400"
                           placeholder="you@company.com">
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-4 py-3 text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400"
                           placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-400">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-slate-900 text-cyan-500"> Remember me
                </label>
                <button type="submit"
                        class="w-full rounded-lg py-3 font-semibold text-white transition hover:opacity-90"
                        style="background:linear-gradient(120deg,#6c5cff,#22a8ff,#00e5ff)">
                    Sign in
                </button>
            </form>
        </div>
        <p class="mt-6 text-center text-xs text-slate-500">Protected area · unauthorized access is prohibited</p>
    </div>
</div>
</body>
</html>
