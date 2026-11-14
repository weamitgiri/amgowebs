@extends('layouts.admin')
@section('title', 'Blog')

@php
    $inp = 'rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $cards = [
        ['Total', $stats['total'], 'bg-slate-900 text-white'],
        ['Published', $stats['published'], 'bg-green-50 text-green-700 border border-green-100'],
        ['Drafts', $stats['draft'], 'bg-amber-50 text-amber-700 border border-amber-100'],
        ['Scheduled', $stats['scheduled'], 'bg-blue-50 text-blue-700 border border-blue-100'],
        ['Categories', $stats['categories'], 'bg-violet-50 text-violet-700 border border-violet-100'],
        ['Total Views', number_format($stats['views']), 'bg-cyan-50 text-cyan-700 border border-cyan-100'],
    ];
    $exportUrl = route('admin.posts.export', request()->only('q', 'category', 'author', 'status', 'from', 'to'));
@endphp

@section('content')
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ($cards as [$label, $value, $cls])
            <div class="rounded-xl px-4 py-3 {{ $cls }}">
                <p class="text-2xl font-bold">{{ $value }}</p>
                <p class="text-xs opacity-80">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <div><label class="mb-1 block text-xs text-slate-500">Search</label><input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Title, slug…" class="{{ $inp }}"></div>
        <div><label class="mb-1 block text-xs text-slate-500">Category</label>
            <select name="category" class="{{ $inp }}"><option value="">All</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(($filters['category'] ?? '') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div><label class="mb-1 block text-xs text-slate-500">Status</label>
            <select name="status" class="{{ $inp }}"><option value="">All</option>
                @foreach (['published' => 'Published', 'draft' => 'Draft', 'scheduled' => 'Scheduled'] as $k => $v)<option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div><label class="mb-1 block text-xs text-slate-500">Author</label>
            <select name="author" class="{{ $inp }}"><option value="">All</option>
                @foreach ($authors as $a)<option value="{{ $a->id }}" @selected(($filters['author'] ?? '') == $a->id)>{{ $a->name }}</option>@endforeach
            </select>
        </div>
        <div><label class="mb-1 block text-xs text-slate-500">From</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="{{ $inp }}"></div>
        <div><label class="mb-1 block text-xs text-slate-500">To</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="{{ $inp }}"></div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filter</button>
        <a href="{{ route('admin.posts.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Reset</a>
        <div class="ml-auto flex gap-2">
            <a href="{{ $exportUrl }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">⬇ Export CSV</a>
            @permission('blog.create')<a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">+ Add New Blog</a>@endpermission
        </div>
    </form>

    <form method="POST" action="{{ route('admin.posts.bulk') }}" x-data="{ sel: [], act: '' }"
          @submit="if(act==='delete' && !confirm('Delete the selected posts? This cannot be undone.')) $event.preventDefault()">
        @csrf
        <div x-show="sel.length" x-cloak class="mb-3 flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm">
            <span class="font-medium text-slate-700"><span x-text="sel.length"></span> selected</span>
            <select name="bulk_action" x-model="act" class="{{ $inp }}">
                <option value="">Bulk action…</option>
                @permission('blog.edit')<option value="publish">Publish</option><option value="draft">Move to draft</option><option value="category">Set category</option>@endpermission
                <option value="export">Export selected (CSV)</option>
                @permission('blog.delete')<option value="delete">Delete</option>@endpermission
            </select>
            <select name="bulk_category_id" x-show="act==='category'" class="{{ $inp }}">
                <option value="">— Uncategorised —</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>
            <button type="submit" :disabled="!act" class="rounded-lg bg-slate-900 px-4 py-1.5 text-white disabled:opacity-40">Apply</button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3"><input type="checkbox" @change="sel = $event.target.checked ? [...$root.querySelectorAll('.rowcb')].map(c=>c.value) : []"></th>
                        <th class="px-3 py-3">Blog</th>
                        <th class="px-3 py-3">Category</th>
                        <th class="px-3 py-3">Author</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">SEO</th>
                        <th class="px-3 py-3">Focus KW</th>
                        <th class="px-3 py-3">Published</th>
                        <th class="px-3 py-3">Updated</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($posts as $post)
                        @php
                            $score = $post->seo_score;
                            $scoreCls = $score >= 80 ? 'bg-green-100 text-green-700' : ($score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                            $state = $post->state;
                            $stateCls = ['published' => 'bg-green-100 text-green-700', 'scheduled' => 'bg-blue-100 text-blue-700', 'draft' => 'bg-slate-200 text-slate-600'][$state];
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3"><input type="checkbox" class="rowcb" name="ids[]" value="{{ $post->id }}" x-model="sel"></td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($post->card_image)
                                        <img src="{{ $post->card_image }}" alt="" class="h-10 w-14 rounded object-cover border border-slate-200">
                                    @else
                                        <span class="grid h-10 w-14 place-items-center rounded bg-slate-100 text-[10px] text-slate-400">no image</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-900 line-clamp-1">{{ $post->title }}</p>
                                        <p class="text-xs text-slate-400">#{{ $post->id }} · /{{ $post->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-600">{{ $post->category?->name ?? '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $post->author?->name ?? '—' }}</td>
                            <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $stateCls }}">{{ ucfirst($state) }}</span></td>
                            <td class="px-3 py-3"><span class="rounded px-2 py-0.5 text-xs font-semibold {{ $scoreCls }}">{{ $score }}</span></td>
                            <td class="px-3 py-3 text-slate-500">{{ $post->seo?->focus_keyword ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-500">{{ optional($post->published_at)->format('M j, Y') ?? '—' }}</td>
                            <td class="px-3 py-3 text-slate-500">{{ $post->updated_at->format('M j, Y') }}</td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex items-center justify-end gap-2 text-xs">
                                    <a href="{{ route('admin.posts.preview', $post) }}" target="_blank" class="text-slate-400 hover:text-slate-700">Preview</a>
                                    @permission('blog.edit')<a href="{{ route('admin.posts.edit', $post) }}" class="text-cyan-600 hover:underline">Edit</a>@endpermission
                                    @permission('blog.create')
                                        <button type="submit" formaction="{{ route('admin.posts.duplicate', $post) }}" class="text-slate-500 hover:underline">Duplicate</button>
                                    @endpermission
                                    @permission('blog.delete')
                                        <button type="submit" formaction="{{ route('admin.posts.destroy', $post) }}" name="_method" value="DELETE"
                                                onclick="return confirm('Delete “{{ \Illuminate\Support\Str::limit($post->title, 40) }}”?')" class="text-red-600 hover:underline">Delete</button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-12 text-center text-slate-400">No blogs match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-5">{{ $posts->links() }}</div>
@endsection
