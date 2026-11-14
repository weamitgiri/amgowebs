@extends('layouts.admin')
@section('title', 'Import preview')

@section('content')
    <a href="{{ route('admin.blog-csv.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back</a>

    <div class="mb-5 flex flex-wrap gap-3">
        <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-green-700"><p class="text-2xl font-bold">{{ $summary['create'] }}</p><p class="text-xs">To create</p></div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-blue-700"><p class="text-2xl font-bold">{{ $summary['update'] }}</p><p class="text-xs">To update (existing)</p></div>
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700"><p class="text-2xl font-bold">{{ $summary['error'] }}</p><p class="text-xs">With errors (skipped)</p></div>
    </div>

    <div class="mb-5 overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Line</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Notes</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($analysis as $item)
                    @php $cls = ['create' => 'bg-green-100 text-green-700', 'update' => 'bg-blue-100 text-blue-700', 'error' => 'bg-red-100 text-red-700'][$item['action']]; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-400">{{ $item['line'] }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800">{{ \Illuminate\Support\Str::limit($item['title'], 60) ?: '—' }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $item['slug'] ?: '—' }}</td>
                        <td class="px-4 py-2"><span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cls }}">{{ ucfirst($item['action']) }}</span></td>
                        <td class="px-4 py-2 text-xs text-red-600">{{ implode(' ', $item['errors']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.blog-csv.import') }}" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <h3 class="mb-3 font-semibold text-slate-800">Import options</h3>
        <div class="mb-4 space-y-2 text-sm text-slate-700">
            <label class="flex items-center gap-2"><input type="hidden" name="update_existing" value="0"><input type="checkbox" name="update_existing" value="1" checked class="rounded border-slate-300"> Update existing blogs (matched by id/slug)</label>
            <label class="flex items-center gap-2"><input type="hidden" name="skip_duplicates" value="0"><input type="checkbox" name="skip_duplicates" value="1" class="rounded border-slate-300"> Skip duplicates instead of updating</label>
        </div>
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Import {{ $summary['create'] + $summary['update'] }} blog(s)</button>
            <a href="{{ route('admin.blog-csv.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 hover:bg-slate-100">Cancel</a>
        </div>
    </form>
@endsection
