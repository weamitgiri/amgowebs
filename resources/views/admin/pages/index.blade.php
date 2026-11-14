@extends('layouts.admin')
@section('title', 'Pages')

@section('content')
    @if ($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Build and manage website pages with reusable sections.</p>
        @permission('pages.create')<a href="{{ route('admin.pages.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ New Page</a>@endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Sections</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($pages as $page)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><p class="font-medium text-slate-900">{{ $page->title }}</p><p class="text-xs text-slate-500">/{{ $page->slug }} @if ($page->is_system)<span class="text-blue-600">· system</span>@endif</p></td>
                        <td class="px-4 py-3 text-slate-500">{{ $page->sections_count }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-600' }}">{{ ucfirst($page->status) }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ url('/'.$page->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>
                                @permission('pages.edit')<a href="{{ route('admin.pages.edit', $page) }}" class="text-cyan-600 hover:underline">Build</a>@endpermission
                                @permission('pages.delete')
                                    @unless ($page->is_system)
                                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                                    @endunless
                                @endpermission
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
