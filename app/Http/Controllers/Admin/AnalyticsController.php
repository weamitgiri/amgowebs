<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $days = (int) $request->query('days', 30);
        $days = in_array($days, [7, 30, 90, 365], true) ? $days : 30;
        $since = now()->subDays($days);
        $base = fn () => Visit::where('created_at', '>=', $since);

        $countries = $base()->whereNotNull('country')
            ->selectRaw('country, country_name, count(*) as hits, count(distinct visitor_hash) as visitors')
            ->groupBy('country', 'country_name')->orderByDesc('hits')->limit(40)->get();

        return view('admin.analytics.index', [
            'days' => $days,
            'total' => $base()->count(),
            'unique' => (int) $base()->distinct('visitor_hash')->count('visitor_hash'),
            'countries' => $countries,
            'maxCountryHits' => max(1, (int) $countries->max('hits')),
            'pages' => $base()->selectRaw('path, count(*) as hits')->groupBy('path')->orderByDesc('hits')->limit(12)->get(),
            'devices' => $base()->whereNotNull('device')->selectRaw('device, count(*) as hits')->groupBy('device')->orderByDesc('hits')->pluck('hits', 'device'),
            'referrers' => $base()->whereNotNull('referrer_host')->selectRaw('referrer_host, count(*) as hits')->groupBy('referrer_host')->orderByDesc('hits')->limit(10)->get(),
            'daily' => $base()->selectRaw('date(created_at) as d, count(*) as hits')->groupBy('d')->orderBy('d')->get(),
            'targets' => ['US', 'GB', 'AU', 'NZ', 'CA'],
        ]);
    }
}
