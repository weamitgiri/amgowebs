@extends('layouts.admin')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')

@php
    $inp = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $lbl = 'mb-1 block text-sm font-medium text-slate-600';
    $seoTitle = old('seo_title', $category->seo?->title);
    $seoDesc = old('seo_description', $category->seo?->description);
@endphp

@section('content')
    <a href="{{ route('admin.categories.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to categories</a>

    @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Please fix the errors below.</div>@endif

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                <div><label class="{{ $lbl }}">Name *</label><input name="name" value="{{ old('name', $category->name) }}" required class="{{ $inp }}">@error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="{{ $lbl }}">Slug</label><input name="slug" value="{{ old('slug', $category->slug) }}" placeholder="auto from name" class="{{ $inp }}">@error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="{{ $lbl }}">Description</label><textarea name="description" rows="3" class="{{ $inp }}">{{ old('description', $category->description) }}</textarea></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                <h3 class="font-semibold text-slate-800">SEO</h3>
                <div><label class="{{ $lbl }}">SEO title</label><input name="seo_title" value="{{ $seoTitle }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Meta description</label><textarea name="seo_description" rows="2" class="{{ $inp }}">{{ $seoDesc }}</textarea></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                <div>
                    <label class="{{ $lbl }}">Parent category</label>
                    <select name="parent_id" class="{{ $inp }}">
                        <option value="">— None (top level) —</option>
                        @foreach ($parents as $p)<option value="{{ $p->id }}" @selected((int) old('parent_id', $category->parent_id) === $p->id)>{{ $p->name }}</option>@endforeach
                    </select>
                    @error('parent_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div><label class="{{ $lbl }}">Sort order</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="{{ $inp }}"></div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded border-slate-300"> Active
                </label>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-3">
                <h3 class="font-semibold text-slate-800">Category image</h3>
                @if ($category->image)<img src="{{ $category->image }}" alt="" class="mb-2 h-28 w-full rounded-lg border border-slate-200 object-cover">@endif
                <input type="file" name="image" accept="image/*" class="text-sm">
                @error('image')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <button class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">{{ $category->exists ? 'Update Category' : 'Create Category' }}</button>
        </div>
    </form>
@endsection
