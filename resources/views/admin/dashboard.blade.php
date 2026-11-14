@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <p class="mb-6 text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}. Here's what's happening.</p>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $cards = [
                ['Total Inquiries', $stats['total_inquiries'], 'text-slate-900'],
                ['New Inquiries', $stats['new_inquiries'], 'text-cyan-600'],
                ['Meetings Scheduled', $stats['meetings_scheduled'], 'text-violet-600'],
                ['Converted Leads', $stats['converted'], 'text-green-600'],
                ['Active Packages', $stats['active_packages'], 'text-slate-900'],
                ['Published Pages', $stats['published_pages'], 'text-slate-900'],
                ['Published Posts', $stats['published_posts'], 'text-slate-900'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $color])
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-1 text-3xl font-bold {{ $color }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    {{-- Pipeline --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold text-slate-700">Lead Pipeline</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ($pipeline as $stage)
                <div class="flex-1 min-w-[120px] rounded-lg bg-slate-50 px-4 py-3 text-center">
                    <p class="text-2xl font-bold text-slate-800">{{ $stage['count'] }}</p>
                    <p class="text-xs text-slate-500">{{ $stage['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Recent inquiries --}}
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Recent Inquiries</h2>
                @if (\Illuminate\Support\Facades\Route::has('admin.inquiries.index'))
                    <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-medium text-cyan-600 hover:underline">View all</a>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentInquiries as $inquiry)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $inquiry->name }}</p>
                            <p class="text-xs text-slate-500">{{ $inquiry->package?->name ?? 'General' }} · {{ $inquiry->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $inquiry->status_label }}</span>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-400">No inquiries yet. They'll appear here once customers submit the project form.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Recent Activity</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentActivity as $log)
                    <div class="px-5 py-3">
                        <p class="text-sm text-slate-700">{{ $log->description ?? $log->action }}</p>
                        <p class="text-xs text-slate-400">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-400">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
