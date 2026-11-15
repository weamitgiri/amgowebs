@extends('layouts.admin')
@section('title', 'Visitors')

@php
    $flag = function ($code) {
        if (! $code || strlen($code) !== 2) return '🌐';
        $a = 0x1F1E6;
        return mb_chr($a + ord(strtoupper($code[0])) - 65).mb_chr($a + ord(strtoupper($code[1])) - 65);
    };
    $targetHits = $countries->whereIn('country', $targets)->sum('hits');
    $topCountry = $countries->first();
    $maxDaily = max(1, (int) $daily->max('hits'));
@endphp

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Where your visitors come from. Privacy-friendly — no raw IPs stored.</p>
        <div class="flex gap-1 rounded-lg border border-slate-200 bg-white p-1">
            @foreach ([7 => '7d', 30 => '30d', 90 => '90d', 365 => '1y'] as $d => $lbl)
                <a href="{{ route('admin.analytics.index', ['days' => $d]) }}" class="rounded-md px-3 py-1.5 text-sm font-medium {{ $days === $d ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $lbl }}</a>
            @endforeach
        </div>
    </div>

    @if ($total === 0)
        <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
            <p class="text-lg font-semibold text-slate-800">No visits tracked yet</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">As real people browse your public site, their country, pages and devices will appear here. Open your site in a browser to generate the first data point.</p>
        </div>
    @else
        {{-- Stat cards --}}
        <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-xl bg-slate-900 px-4 py-3 text-white"><p class="text-2xl font-bold">{{ number_format($total) }}</p><p class="text-xs opacity-80">Page views</p></div>
            <div class="rounded-xl border border-cyan-100 bg-cyan-50 px-4 py-3 text-cyan-700"><p class="text-2xl font-bold">{{ number_format($unique) }}</p><p class="text-xs opacity-80">Unique visitors</p></div>
            <div class="rounded-xl border border-violet-100 bg-violet-50 px-4 py-3 text-violet-700"><p class="text-2xl font-bold">{{ $countries->count() }}</p><p class="text-xs opacity-80">Countries</p></div>
            <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-green-700"><p class="text-2xl font-bold">{{ $total ? round($targetHits / $total * 100) : 0 }}%</p><p class="text-xs opacity-80">From target countries</p></div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Countries --}}
            <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="mb-4 font-semibold text-slate-800">Visitors by country</h3>
                <div class="space-y-2">
                    @foreach ($countries as $c)
                        @php $isTarget = in_array($c->country, $targets, true); @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-lg">{{ $flag($c->country) }}</span>
                            <span class="w-40 truncate text-sm {{ $isTarget ? 'font-semibold text-slate-900' : 'text-slate-600' }}">{{ $c->country_name ?: $c->country }}@if ($isTarget) <span class="ml-1 rounded bg-green-100 px-1 text-[10px] text-green-700">target</span>@endif</span>
                            <div class="h-2.5 flex-1 rounded-full bg-slate-100"><div class="h-2.5 rounded-full {{ $isTarget ? 'bg-green-500' : 'bg-slate-400' }}" style="width:{{ max(3, round($c->hits / $maxCountryHits * 100)) }}%"></div></div>
                            <span class="w-20 text-right text-sm text-slate-700">{{ number_format($c->hits) }} <span class="text-xs text-slate-400">({{ $c->visitors }})</span></span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-slate-400">Bars show page views; number in brackets is unique visitors. Target countries (US, UK, AU, NZ, CA) highlighted.</p>
            </div>

            {{-- Devices + trend --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-slate-800">Devices</h3>
                    @forelse ($devices as $device => $hits)
                        <div class="mb-2 flex items-center justify-between text-sm"><span class="capitalize text-slate-600">{{ $device }}</span><span class="font-medium text-slate-800">{{ $total ? round($hits / $total * 100) : 0 }}%</span></div>
                    @empty
                        <p class="text-sm text-slate-400">—</p>
                    @endforelse
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-slate-800">Daily views</h3>
                    <div class="flex h-20 items-end gap-0.5">
                        @foreach ($daily as $d)
                            <div class="flex-1 rounded-t bg-cyan-400" style="height:{{ max(4, round($d->hits / $maxDaily * 100)) }}%" title="{{ $d->d }}: {{ $d->hits }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Pages + referrers --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="mb-3 font-semibold text-slate-800">Top pages</h3>
                <table class="w-full text-sm">
                    @foreach ($pages as $p)
                        <tr class="border-b border-slate-50"><td class="py-1.5 text-slate-600 truncate">{{ $p->path }}</td><td class="py-1.5 text-right font-medium text-slate-800">{{ number_format($p->hits) }}</td></tr>
                    @endforeach
                </table>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="mb-3 font-semibold text-slate-800">Top referrers</h3>
                @forelse ($referrers as $r)
                    <div class="flex items-center justify-between border-b border-slate-50 py-1.5 text-sm"><span class="text-slate-600">{{ $r->referrer_host }}</span><span class="font-medium text-slate-800">{{ number_format($r->hits) }}</span></div>
                @empty
                    <p class="text-sm text-slate-400">No external referrers yet (direct / bookmarks).</p>
                @endforelse
            </div>
        </div>

        <p class="mt-5 text-xs text-slate-400">Tip: for the most accurate country data with zero overhead, put the site behind Cloudflare (free) — its country header is used automatically. Google Analytics (via your GTM) remains the full analytics source.</p>
    @endif
@endsection
