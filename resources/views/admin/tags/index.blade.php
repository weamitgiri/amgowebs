@extends('layouts.admin')
@section('title', 'Blog Tags')

@php $inp = 'rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none'; @endphp

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        @permission('blog.edit')
            <div class="rounded-xl border border-slate-200 bg-white p-6 h-fit">
                <h3 class="mb-3 font-semibold text-slate-800">Add tag</h3>
                <form method="POST" action="{{ route('admin.tags.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" placeholder="Tag name" required class="{{ $inp }} w-full">
                    @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    <button class="w-full rounded-lg bg-slate-900 py-2 text-sm font-medium text-white hover:bg-slate-700">Add Tag</button>
                </form>
            </div>
        @endpermission

        <div class="lg:col-span-2 overflow-hidden rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-4 py-3">Tag</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Posts</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tags as $tag)
                        <tr class="hover:bg-slate-50">
                            @permission('blog.edit')
                                <td colspan="2" class="px-4 py-2">
                                    <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input name="name" value="{{ $tag->name }}" class="{{ $inp }} w-40">
                                        <input name="slug" value="{{ $tag->slug }}" class="{{ $inp }} w-40 text-slate-500">
                                        <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs hover:bg-slate-100">Save</button>
                                    </form>
                                </td>
                            @else
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $tag->name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $tag->slug }}</td>
                            @endpermission
                            <td class="px-4 py-3 text-slate-500">{{ $tag->posts_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ url('/blog/tag/'.$tag->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>
                                    @permission('blog.delete')
                                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete “{{ $tag->name }}”?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-slate-400">No tags yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
