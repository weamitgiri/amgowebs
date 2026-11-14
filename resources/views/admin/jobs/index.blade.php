@extends('layouts.admin')
@section('title', 'Careers')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Manage your open job positions. Published roles appear on <a href="{{ url('/careers') }}" target="_blank" class="text-cyan-600 hover:underline">/careers</a>.</p>
        @permission('jobs.create')
            <a href="{{ route('admin.jobs.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Opening</a>
        @endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Published</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jobs as $job)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $job->title }}</p>
                            <p class="text-xs text-slate-500">/careers/{{ $job->slug }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $job->type_label }}@if ($job->is_remote) · Remote @endif</td>
                        <td class="px-4 py-3 text-slate-500">{{ $job->department ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @permission('jobs.edit')
                                <form method="POST" action="{{ route('admin.jobs.toggle', $job) }}">
                                    @csrf
                                    <button class="rounded-full px-2.5 py-1 text-xs font-medium {{ $job->is_published ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-500' }}">{{ $job->is_published ? 'Published' : 'Draft' }}</button>
                                </form>
                            @else
                                <span class="text-xs">{{ $job->is_published ? 'Published' : 'Draft' }}</span>
                            @endpermission
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if ($job->is_published)<a href="{{ url('/careers/'.$job->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>@endif
                                @permission('jobs.edit')<a href="{{ route('admin.jobs.edit', $job) }}" class="text-cyan-600 hover:underline">Edit</a>@endpermission
                                @permission('jobs.delete')
                                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Delete “{{ $job->title }}”?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">No job openings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
