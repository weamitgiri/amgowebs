@extends('layouts.admin')
@section('title', 'Blog Categories')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Organise your blog into categories and nested subcategories.</p>
        @permission('blog.edit')
            <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Category</a>
        @endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Parent</th>
                    <th class="px-4 py-3">Posts</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($category->image)
                                    <img src="{{ $category->image }}" alt="" class="h-9 w-9 rounded object-cover border border-slate-200">
                                @else
                                    <span class="grid h-9 w-9 place-items-center rounded bg-slate-100 text-xs text-slate-400">—</span>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                    <p class="text-xs text-slate-500">/blog/category/{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $category->parent?->name ?? '—' }}@if ($category->children_count) · {{ $category->children_count }} sub @endif</td>
                        <td class="px-4 py-3 text-slate-500">{{ $category->posts_count }}</td>
                        <td class="px-4 py-3">
                            @permission('blog.edit')
                                <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                    @csrf
                                    <button class="rounded-full px-2.5 py-1 text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-500' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</button>
                                </form>
                            @else
                                <span class="text-xs">{{ $category->is_active ? 'Active' : 'Hidden' }}</span>
                            @endpermission
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ url('/blog/category/'.$category->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>
                                @permission('blog.edit')<a href="{{ route('admin.categories.edit', $category) }}" class="text-cyan-600 hover:underline">Edit</a>@endpermission
                                @permission('blog.delete')
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete “{{ $category->name }}”? Its posts become uncategorised and subcategories move to top level.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
