@extends('layouts.admin')
@section('title', $post->exists ? 'Edit Post' : 'New Post')

@php
    $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $tags = old('tags', $post->exists ? $post->tags->pluck('name')->implode(', ') : '');
    $seoTitle = old('seo_title', $post->seo?->title);
    $seoDesc = old('seo_description', $post->seo?->description);
@endphp

@section('content')
    <a href="{{ route('admin.posts.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to posts</a>

    @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Please fix the errors below.</div>@endif

    <form method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Title *</label><input name="title" value="{{ old('title', $post->title) }}" required class="{{ $input }}">@error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Slug</label><input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="auto from title" class="{{ $input }}">@error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Excerpt</label><textarea name="excerpt" rows="2" class="{{ $input }}">{{ old('excerpt', $post->excerpt) }}</textarea></div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-600">Body (HTML allowed)</label>
                        <textarea name="body" rows="14" class="{{ $input }} font-mono text-xs">{{ old('body', $post->body) }}</textarea>
                        <p class="mt-1 text-xs text-slate-400">Supports HTML. A rich-text editor can be layered on this field later.</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">SEO</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">SEO title</label><input name="seo_title" value="{{ $seoTitle }}" class="{{ $input }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Meta description</label><textarea name="seo_description" rows="2" class="{{ $input }}">{{ $seoDesc }}</textarea></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Publish</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
                        <select name="status" class="{{ $input }}">
                            <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                        </select>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Publish date <span class="text-slate-400">(future = scheduled)</span></label><input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="{{ $input }}"></div>
                    <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">{{ $post->exists ? 'Update Post' : 'Create Post' }}</button>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Organize</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Category</label>
                        <select name="category_id" class="{{ $input }}">
                            <option value="">— None —</option>
                            @foreach ($categories as $cat)<option value="{{ $cat->id }}" @selected((int) old('category_id', $post->category_id) === $cat->id)>{{ $cat->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">…or new category</label><input name="new_category" value="{{ old('new_category') }}" placeholder="New category name" class="{{ $input }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Tags</label><input name="tags" value="{{ $tags }}" placeholder="comma, separated, tags" class="{{ $input }}"></div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-3">
                    <h3 class="font-semibold text-slate-800">Featured image</h3>
                    @if ($post->featured_image)<img src="{{ $post->featured_image }}" alt="" class="mb-2 h-28 w-full rounded-lg border border-slate-200 object-cover">@endif
                    <input type="file" name="featured_image" accept="image/*" class="text-sm">
                    @error('featured_image')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </form>
@endsection
