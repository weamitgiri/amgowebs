@extends('layouts.admin')
@section('title', 'Import report')

@section('content')
    <h2 class="mb-1 text-lg font-semibold text-slate-900">Import complete</h2>
    <p class="mb-5 text-sm text-slate-500">Here's what happened.</p>

    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-green-700"><p class="text-2xl font-bold">{{ $report['created'] }}</p><p class="text-xs">Created</p></div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-blue-700"><p class="text-2xl font-bold">{{ $report['updated'] }}</p><p class="text-xs">Updated</p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-600"><p class="text-2xl font-bold">{{ $report['skipped'] }}</p><p class="text-xs">Skipped</p></div>
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700"><p class="text-2xl font-bold">{{ $report['failed'] }}</p><p class="text-xs">Failed</p></div>
    </div>

    @if (! empty($report['messages']))
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6">
            <h3 class="mb-3 font-semibold text-slate-800">Details</h3>
            <ul class="space-y-1 text-sm text-slate-600">
                @foreach ($report['messages'] as $msg)<li class="border-l-2 border-red-300 pl-3">{{ $msg }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="flex gap-2">
        <a href="{{ route('admin.posts.index') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">View blogs</a>
        <a href="{{ route('admin.blog-csv.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 hover:bg-slate-100">Import more</a>
    </div>
@endsection
