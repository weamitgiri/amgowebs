@extends('layouts.admin')
@section('title', 'Inquiries')

@php
    $badge = [
        'new' => 'bg-cyan-100 text-cyan-700', 'under_review' => 'bg-amber-100 text-amber-700',
        'meeting_scheduled' => 'bg-violet-100 text-violet-700', 'proposal_sent' => 'bg-blue-100 text-blue-700',
        'converted' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700',
        'closed' => 'bg-slate-200 text-slate-600',
    ];
@endphp

@section('content')
    {{-- Status filter tabs --}}
    <div class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('admin.inquiries.index') }}" @class(['rounded-full px-4 py-1.5 text-sm font-medium', 'bg-slate-900 text-white' => !request('status'), 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' => request('status')])>
            All <span class="opacity-60">{{ $statusCounts->sum() }}</span>
        </a>
        @foreach ($statuses as $key => $label)
            <a href="{{ route('admin.inquiries.index', ['status' => $key]) }}" @class(['rounded-full px-4 py-1.5 text-sm font-medium', 'bg-slate-900 text-white' => request('status') === $key, 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' => request('status') !== $key])>
                {{ $label }} <span class="opacity-60">{{ $statusCounts[$key] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Search + export --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, email, reference…" class="w-64 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <select name="package" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All packages</option>
                @foreach ($packages as $p)<option value="{{ $p->id }}" @selected((int)($filters['package'] ?? 0) === $p->id)>{{ $p->name }}</option>@endforeach
            </select>
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filter</button>
            @if (array_filter($filters))<a href="{{ route('admin.inquiries.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>@endif
        </form>
        @permission('inquiries.export')
            <a href="{{ route('admin.inquiries.export', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Export CSV</a>
        @endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Reference / Customer</th>
                    <th class="px-4 py-3">Package</th>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Assigned</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($inquiries as $inquiry)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="font-medium text-slate-900 hover:underline">{{ $inquiry->name }}</a>
                            <div class="text-xs text-slate-500">{{ $inquiry->reference }} · {{ $inquiry->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $inquiry->package?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $inquiry->country }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badge[$inquiry->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $inquiry->status_label }}</span></td>
                        <td class="px-4 py-3 text-slate-600">{{ $inquiry->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $inquiry->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-cyan-600 hover:underline">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">No inquiries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $inquiries->links() }}</div>
@endsection
