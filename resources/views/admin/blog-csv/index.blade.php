@extends('layouts.admin')
@section('title', 'Blog CSV Import / Export')

@section('content')
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- EXPORT --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h3 class="mb-1 font-semibold text-slate-800">Export blogs</h3>
            <p class="mb-4 text-sm text-slate-500">{{ $count }} blog(s). Exports are UTF-8 (Excel-ready) with all {{ count(\App\Services\BlogCsvService::COLUMNS) }} columns.</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.blog-csv.export', ['scope' => 'all']) }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Export all</a>
                <a href="{{ route('admin.blog-csv.export', ['scope' => 'published']) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Published only</a>
                <a href="{{ route('admin.blog-csv.export', ['scope' => 'draft']) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Drafts only</a>
            </div>
            <form method="GET" action="{{ route('admin.blog-csv.export') }}" class="mt-3 flex items-center gap-2">
                <select name="category" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Export by category…</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Export</button>
            </form>
        </div>

        {{-- IMPORT --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h3 class="mb-1 font-semibold text-slate-800">Import blogs</h3>
            <p class="mb-4 text-sm text-slate-500">Upload a CSV — you'll preview and confirm before anything is saved.</p>

            @if (session('error'))<div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ session('error') }}</div>@endif

            <a href="{{ route('admin.blog-csv.template') }}" class="mb-4 inline-block rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">⬇ Download sample template</a>

            <form method="POST" action="{{ route('admin.blog-csv.preview') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="w-full text-sm">
                @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                <button class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">Upload &amp; preview</button>
            </form>

            <div class="mt-4 rounded-lg bg-slate-50 p-3 text-xs text-slate-500">
                <p class="font-medium text-slate-600">Format tips</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-4">
                    <li>UTF-8, Hindi &amp; English supported. Multiline HTML content is fine inside quotes.</li>
                    <li><strong>tags</strong>: comma-separated. <strong>faq_questions</strong> / <strong>faq_answers</strong>: separate each with <code>|</code> (paired by order).</li>
                    <li>Rows match existing blogs by <strong>id</strong> then <strong>slug</strong>. Empty cells never overwrite existing values.</li>
                    <li>Slug &amp; reading time are generated when blank. Scripts are stripped from content.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
