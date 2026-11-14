@extends('layouts.admin')
@section('title', 'SEO Manager')

@php
    $sev = [
        'high' => ['High', 'bg-red-100 text-red-700'], 'medium' => ['Medium', 'bg-amber-100 text-amber-700'],
        'low' => ['Low', 'bg-slate-200 text-slate-600'], 'info' => ['Info', 'bg-blue-100 text-blue-700'],
    ];
    $input = 'rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
@endphp

@section('content')
    {{-- Severity summary --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ($sev as $key => [$label, $class])
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">{{ $label }} issues</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $severityCounts[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            {{-- Audit --}}
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4"><h2 class="text-sm font-semibold text-slate-700">SEO Audit ({{ count($issues) }} findings)</h2></div>
                <div class="max-h-[460px] overflow-y-auto divide-y divide-slate-100">
                    @forelse ($issues as $issue)
                        <div class="flex items-center justify-between gap-3 px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $sev[$issue['severity']][1] }}">{{ $sev[$issue['severity']][0] }}</span>
                                <div>
                                    <p class="text-sm text-slate-800">{{ $issue['message'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $issue['type'] }} · {{ $issue['item'] }}</p>
                                </div>
                            </div>
                            @if ($issue['link'])<a href="{{ $issue['link'] }}" class="text-xs font-medium text-cyan-600 hover:underline">Fix</a>@endif
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-sm text-green-600">✓ No SEO issues found. Everything looks good!</div>
                    @endforelse
                </div>
            </div>

            {{-- Redirects --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-700">301 / 302 Redirects</h2>
                @permission('seo.manage')
                    <form method="POST" action="{{ route('admin.seo.redirects.store') }}" class="mb-4 flex flex-wrap items-center gap-2 border-b border-slate-100 pb-4">
                        @csrf
                        <input name="from_path" placeholder="/old-url" class="{{ $input }} flex-1 min-w-[120px]" required>
                        <span class="text-slate-400">→</span>
                        <input name="to_path" placeholder="/new-url" class="{{ $input }} flex-1 min-w-[120px]" required>
                        <select name="status_code" class="{{ $input }}"><option value="301">301</option><option value="302">302</option></select>
                        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Add</button>
                    </form>
                @endpermission
                <div class="space-y-2">
                    @forelse ($redirects as $r)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                            <span class="text-slate-700"><code>{{ $r->from_path }}</code> → <code>{{ $r->to_path }}</code> <span class="text-xs text-slate-400">({{ $r->status_code }}, {{ $r->hits }} hits)</span></span>
                            @permission('seo.manage')
                                <form method="POST" action="{{ route('admin.seo.redirects.destroy', $r) }}" onsubmit="return confirm('Remove redirect?')">@csrf @method('DELETE')<button class="text-xs text-red-600 hover:underline">Remove</button></form>
                            @endpermission
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No redirects configured.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Globals + technical --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">Global SEO Defaults</h2>
                <dl class="space-y-2 text-sm">
                    @foreach ($globals as $label => $value)
                        <div><dt class="text-xs text-slate-400">{{ $label }}</dt><dd class="text-slate-700">{{ $value ?: '—' }}</dd></div>
                    @endforeach
                </dl>
                @permission('settings.manage')
                    <a href="{{ route('admin.settings.edit') }}" class="mt-3 inline-block text-xs font-medium text-cyan-600 hover:underline">Edit in Settings →</a>
                @endpermission
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">Technical SEO</h2>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center justify-between"><span class="text-slate-600">XML Sitemap</span><a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-cyan-600 hover:underline">View ↗</a></li>
                    <li class="flex items-center justify-between"><span class="text-slate-600">robots.txt</span><a href="{{ url('/robots.txt') }}" target="_blank" class="text-cyan-600 hover:underline">View ↗</a></li>
                    <li class="flex items-center justify-between"><span class="text-slate-600">Structured data</span><span class="text-green-600">✓ Active</span></li>
                </ul>
            </div>
        </div>
    </div>
@endsection
