@extends('layouts.admin')
@section('title', 'New Page')

@php $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none'; @endphp

@section('content')
    <a href="{{ route('admin.pages.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to pages</a>
    @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Please fix the errors below.</div>@endif

    <form method="POST" action="{{ route('admin.pages.store') }}" class="max-w-2xl">
        @csrf
        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Page title *</label><input name="title" value="{{ old('title') }}" required class="{{ $input }}">@error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Slug (URL)</label><input name="slug" value="{{ old('slug') }}" placeholder="auto from title" class="{{ $input }}">@error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Excerpt</label><textarea name="excerpt" rows="2" class="{{ $input }}">{{ old('excerpt') }}</textarea></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
                    <select name="status" class="{{ $input }}"><option value="draft">Draft</option><option value="published">Published</option></select>
                </div>
                <label class="flex items-center gap-2 pt-7 text-sm text-slate-700"><input type="checkbox" name="is_in_sitemap" value="1" checked> Include in sitemap</label>
            </div>
            <div class="border-t border-slate-100 pt-4"><button class="rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Create &amp; Add Sections →</button></div>
        </div>
    </form>
@endsection
