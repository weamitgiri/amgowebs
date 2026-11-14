<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use App\Models\PackageInquiry;
use App\Models\Page;
use App\Models\Post;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_inquiries' => PackageInquiry::count(),
            'new_inquiries' => PackageInquiry::where('status', 'new')->count(),
            'meetings_scheduled' => PackageInquiry::where('status', 'meeting_scheduled')->count(),
            'converted' => PackageInquiry::where('status', 'converted')->count(),
            'active_packages' => Package::published()->count(),
            'published_pages' => Page::where('status', 'published')->count(),
            'published_posts' => Post::where('status', 'published')->count(),
        ];

        $recentInquiries = PackageInquiry::with('package')->latest()->limit(8)->get();
        $recentActivity = ActivityLog::with('user')->latest()->limit(8)->get();

        $pipeline = collect(PackageInquiry::STATUSES)->map(fn ($label, $key) => [
            'label' => $label,
            'count' => PackageInquiry::where('status', $key)->count(),
        ])->values();

        return view('admin.dashboard', compact('stats', 'recentInquiries', 'recentActivity', 'pipeline'));
    }
}
