@extends('layouts.admin')
@section('title', 'Activity Log')

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="user" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All users</option>
            @foreach ($users as $u)<option value="{{ $u->id }}" @selected((int)($filters['user'] ?? 0) === $u->id)>{{ $u->name }}</option>@endforeach
        </select>
        <select name="action" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All actions</option>
            @foreach ($actions as $a)<option value="{{ $a }}" @selected(($filters['action'] ?? '') === $a)>{{ $a }}</option>@endforeach
        </select>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filter</button>
        @if (array_filter($filters))<a href="{{ route('admin.activity.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>@endif
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Action</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">User</th><th class="px-4 py-3">IP</th><th class="px-4 py-3">When</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-600">{{ $log->action }}</code></td>
                        <td class="px-4 py-3 text-slate-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3 text-slate-500" title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">No activity recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
