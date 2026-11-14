@extends('layouts.admin')
@section('title', 'Build: ' . $page->title)

@php
    $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $seoTitle = old('seo_title', $page->seo?->title);
    $seoDesc = old('seo_description', $page->seo?->description);
@endphp

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.pages.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Back to pages</a>
        <a href="{{ url('/'.$page->slug) }}" target="_blank" class="text-sm font-medium text-cyan-600 hover:underline">Preview page ↗</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Sections builder --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-slate-800">Page Sections</h2>
                {{-- Add section --}}
                <form method="POST" action="{{ route('admin.pages.sections.add', $page) }}" class="flex gap-2">
                    @csrf
                    <select name="type" class="{{ $input }} w-48" required>
                        <option value="">+ Add section…</option>
                        @foreach ($sectionTypes as $key => $type)
                            @if (empty($type['restricted']) || auth()->user()->isSuperAdmin())
                                <option value="{{ $key }}">{{ $type['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Add</button>
                </form>
            </div>

            @if ($page->sections->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-400">No sections yet. Add one above to start building.</div>
            @endif

            <div id="sections" data-reorder-url="{{ route('admin.pages.sections.reorder', $page) }}">
                @foreach ($page->sections as $section)
                    @php $schema = $sectionTypes[$section->type] ?? ['label' => $section->type, 'fields' => []]; @endphp
                    <div class="section-card mb-3 rounded-xl border border-slate-200 bg-white" data-id="{{ $section->id }}" x-data="{ open: false, items: @js($section->data['items'] ?? []) }">
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="drag-handle cursor-move text-slate-300 hover:text-slate-500" title="Drag to reorder">⠿</span>
                                <span class="font-medium text-slate-800">{{ $schema['label'] }}</span>
                                @unless ($section->is_visible)<span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs text-slate-500">Hidden</span>@endunless
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <button type="button" @click="open=!open" class="text-cyan-600 hover:underline" x-text="open ? 'Close' : 'Edit'"></button>
                                <form method="POST" action="{{ route('admin.pages.sections.duplicate', [$page, $section]) }}">@csrf<button class="text-slate-500 hover:underline">Duplicate</button></form>
                                <form method="POST" action="{{ route('admin.pages.sections.delete', [$page, $section]) }}" onsubmit="return confirm('Remove this section?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                            </div>
                        </div>

                        {{-- Edit form --}}
                        <form method="POST" action="{{ route('admin.pages.sections.update', [$page, $section]) }}" x-show="open" style="display:none" class="border-t border-slate-100 p-4 space-y-3">
                            @csrf @method('PUT')
                            @foreach ($schema['fields'] as $field)
                                @if ($field['type'] === 'repeater')
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-slate-600">{{ $field['label'] }}</label>
                                        <template x-for="(row, i) in items" :key="i">
                                            <div class="mb-2 grid gap-2 rounded-lg bg-slate-50 p-2" style="grid-template-columns: repeat({{ count($field['fields']) }}, 1fr) auto">
                                                @foreach ($field['fields'] as $sub)
                                                    <input :name="`data[{{ $field['key'] }}][${i}][{{ $sub['key'] }}]`" x-model="row['{{ $sub['key'] }}']" placeholder="{{ $sub['label'] }}" class="rounded border border-slate-300 px-2 py-1.5 text-sm">
                                                @endforeach
                                                <button type="button" @click="items.splice(i,1)" class="rounded border border-slate-200 px-2 text-slate-400 hover:bg-white">&times;</button>
                                            </div>
                                        </template>
                                        <button type="button" @click="items.push({})" class="text-sm font-medium text-cyan-600 hover:underline">+ Add item</button>
                                    </div>
                                @elseif ($field['type'] === 'select')
                                    <div><label class="mb-1 block text-sm font-medium text-slate-600">{{ $field['label'] }}</label>
                                        <select name="data[{{ $field['key'] }}]" class="{{ $input }}">
                                            @foreach ($field['options'] as $v => $l)<option value="{{ $v }}" @selected(($section->data[$field['key']] ?? '') === $v)>{{ $l }}</option>@endforeach
                                        </select>
                                    </div>
                                @elseif (in_array($field['type'], ['textarea', 'html']))
                                    <div><label class="mb-1 block text-sm font-medium text-slate-600">{{ $field['label'] }}</label>
                                        <textarea name="data[{{ $field['key'] }}]" rows="{{ $field['type'] === 'html' ? 6 : 2 }}" class="{{ $input }} {{ $field['type'] === 'html' ? 'font-mono text-xs' : '' }}">{{ $section->data[$field['key']] ?? '' }}</textarea>
                                    </div>
                                @else
                                    <div><label class="mb-1 block text-sm font-medium text-slate-600">{{ $field['label'] }}</label>
                                        <input name="data[{{ $field['key'] }}]" value="{{ $section->data[$field['key']] ?? '' }}" class="{{ $input }}">
                                    </div>
                                @endif
                            @endforeach
                            <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_visible" value="1" @checked($section->is_visible)> Visible on page</label>
                            <div class="border-t border-slate-100 pt-3"><button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700">Save section</button></div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Page meta --}}
        <div class="space-y-6">
            <form method="POST" action="{{ route('admin.pages.update', $page) }}">
                @csrf @method('PUT')
                <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                    <h3 class="font-semibold text-slate-800">Page Settings</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Title</label><input name="title" value="{{ $page->title }}" class="{{ $input }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Slug @if ($page->is_system)<span class="text-xs text-slate-400">(system)</span>@endif</label><input name="slug" value="{{ $page->slug }}" class="{{ $input }}" {{ $page->is_system ? 'readonly' : '' }}></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Excerpt</label><textarea name="excerpt" rows="2" class="{{ $input }}">{{ $page->excerpt }}</textarea></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
                        <select name="status" class="{{ $input }}"><option value="draft" @selected($page->status==='draft')>Draft</option><option value="published" @selected($page->status==='published')>Published</option></select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_in_sitemap" value="1" @checked($page->is_in_sitemap)> Include in sitemap</label>
                    <div class="border-t border-slate-100 pt-3 space-y-3">
                        <h4 class="text-sm font-semibold text-slate-700">SEO</h4>
                        <input name="seo_title" value="{{ $seoTitle }}" placeholder="SEO title" class="{{ $input }}">
                        <textarea name="seo_description" rows="2" placeholder="Meta description" class="{{ $input }}">{{ $seoDesc }}</textarea>
                        <select name="robots" class="{{ $input }}">
                            <option value="index, follow" @selected(($page->seo?->robots ?? '')==='index, follow')>index, follow</option>
                            <option value="noindex, follow" @selected(($page->seo?->robots ?? '')==='noindex, follow')>noindex, follow</option>
                            <option value="noindex, nofollow" @selected(($page->seo?->robots ?? '')==='noindex, nofollow')>noindex, nofollow</option>
                        </select>
                    </div>
                    <button class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Save Page</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>
    <script>
        (function () {
            const el = document.getElementById('sections');
            if (!el || !window.Sortable) return;
            Sortable.create(el, {
                handle: '.drag-handle', animation: 150,
                onEnd: function () {
                    const order = [...el.querySelectorAll('.section-card')].map(c => c.dataset.id);
                    const body = new FormData();
                    body.append('_token', document.querySelector('meta[name=csrf-token]').content);
                    order.forEach(id => body.append('order[]', id));
                    fetch(el.dataset.reorderUrl, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                }
            });
        })();
    </script>
@endsection
