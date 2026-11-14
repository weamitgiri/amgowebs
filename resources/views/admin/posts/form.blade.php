@extends('layouts.admin')
@section('title', $post->exists ? 'Edit Blog' : 'New Blog')

@php
    $inp = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $lbl = 'mb-1 block text-sm font-medium text-slate-600';
    $tagsVal = old('tags', $post->exists ? $post->tags->pluck('name')->implode(', ') : '');
    $seoTitle = old('seo_title', $post->seo?->title);
    $seoDesc = old('seo_description', $post->seo?->description);
    $robots = $post->seo?->robots ?? 'index, follow';
    $faqs = old('faqs', $post->exists ? $post->faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer, 'is_active' => (bool) $f->is_active])->values()->all() : []);
    $initial = [
        'faqs' => $faqs,
        'seoTitle' => $seoTitle ?: '',
        'seoDesc' => $seoDesc ?: '',
        'focus' => old('focus_keyword', $post->seo?->focus_keyword) ?: '',
        'title' => old('title', $post->title) ?: '',
        'alt' => old('image_alt', $post->image_alt) ?: '',
        'hasImage' => (bool) $post->featured_image,
        'uploadUrl' => route('admin.posts.image-upload'),
        'csrf' => csrf_token(),
    ];
@endphp

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
<style>[x-cloak]{display:none!important}.note-editor{border-radius:.5rem}.tabbtn.active{background:#0f172a;color:#fff}</style>
@endpush

@section('content')
    <a href="{{ route('admin.posts.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to blogs</a>

    @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Please fix the errors below.</div>@endif

    <form method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data"
          x-data="postForm({{ \Illuminate\Support\Js::from($initial) }})" x-init="initEditor()">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        {{-- Tabs --}}
        <div class="mb-5 flex flex-wrap gap-2 border-b border-slate-200 pb-3">
            @foreach (['content' => 'Content', 'seo' => 'SEO Wizard', 'faqs' => 'FAQs'] as $k => $label)
                <button type="button" @click="tab='{{ $k }}'" :class="tab==='{{ $k }}' ? 'active' : 'bg-white border border-slate-200 text-slate-600'" class="tabbtn rounded-lg px-4 py-2 text-sm font-medium">
                    {{ $label }}@if ($k === 'faqs') <span class="text-xs opacity-70">(<span x-text="faqs.length"></span>)</span>@endif@if ($k === 'seo') <span class="rounded px-1.5 text-xs" :class="score>=80?'bg-green-200 text-green-800':(score>=50?'bg-amber-200 text-amber-800':'bg-red-200 text-red-800')" x-text="score"></span>@endif
                </button>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- CONTENT TAB --}}
                <div x-show="tab==='content'" class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <div><label class="{{ $lbl }}">Blog Title *</label><input name="title" x-model="title" required class="{{ $inp }}">@error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="{{ $lbl }}">Slug</label><input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="auto from title" class="{{ $inp }}">@error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="{{ $lbl }}">Excerpt / short description</label><textarea name="excerpt" rows="2" class="{{ $inp }}">{{ old('excerpt', $post->excerpt) }}</textarea></div>
                    <div>
                        <label class="{{ $lbl }}">Content</label>
                        <textarea id="body" name="body">{{ old('body', $post->body) }}</textarea>
                    </div>
                </div>

                {{-- SEO WIZARD TAB --}}
                <div x-show="tab==='seo'" x-cloak class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <h3 class="font-semibold text-slate-800">Primary SEO</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="{{ $lbl }}">Focus keyword</label><input name="focus_keyword" x-model="seo.focus" class="{{ $inp }}" placeholder="e.g. small business website"></div>
                            <div><label class="{{ $lbl }}">Search intent</label>
                                <select name="search_intent" class="{{ $inp }}">
                                    @php $intent = old('search_intent', $post->seo?->search_intent); @endphp
                                    <option value="">—</option>
                                    @foreach (['Informational', 'Commercial', 'Transactional', 'Navigational'] as $i)<option value="{{ $i }}" @selected($intent === $i)>{{ $i }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div><label class="{{ $lbl }}">Secondary keywords <span class="text-slate-400">(comma separated)</span></label><input name="secondary_keywords" value="{{ old('secondary_keywords', $post->seo?->secondary_keywords) }}" class="{{ $inp }}"></div>
                        <div>
                            <label class="{{ $lbl }}">SEO title <span class="text-xs" :class="(titleLen>=30&&titleLen<=65)?'text-green-600':'text-amber-600'">(<span x-text="titleLen"></span>/65)</span></label>
                            <input name="seo_title" x-model="seo.title" class="{{ $inp }}" placeholder="Defaults to the blog title">
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Meta description <span class="text-xs" :class="(descLen>=50&&descLen<=160)?'text-green-600':'text-amber-600'">(<span x-text="descLen"></span>/160)</span></label>
                            <textarea name="seo_description" x-model="seo.desc" rows="2" class="{{ $inp }}"></textarea>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <h3 class="font-semibold text-slate-800">Technical &amp; Social</h3>
                        <div><label class="{{ $lbl }}">Canonical URL</label><input name="canonical_url" value="{{ old('canonical_url', $post->seo?->canonical_url) }}" class="{{ $inp }}" placeholder="Leave blank for the default"></div>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 text-sm text-slate-700"><input type="hidden" name="robots_index" value="0"><input type="checkbox" name="robots_index" value="1" @checked(old('robots_index', ! str_contains($robots, 'noindex'))) class="rounded border-slate-300"> Index</label>
                            <label class="flex items-center gap-2 text-sm text-slate-700"><input type="hidden" name="robots_follow" value="0"><input type="checkbox" name="robots_follow" value="1" @checked(old('robots_follow', ! str_contains($robots, 'nofollow'))) class="rounded border-slate-300"> Follow</label>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="{{ $lbl }}">Open Graph title</label><input name="og_title" value="{{ old('og_title', $post->seo?->og_title) }}" class="{{ $inp }}"></div>
                            <div><label class="{{ $lbl }}">Open Graph image URL</label><input name="og_image" value="{{ old('og_image', $post->seo?->og_image) }}" class="{{ $inp }}" placeholder="Defaults to the featured image"></div>
                        </div>
                        <div><label class="{{ $lbl }}">Open Graph description</label><textarea name="og_description" rows="2" class="{{ $inp }}">{{ old('og_description', $post->seo?->og_description) }}</textarea></div>
                    </div>

                    {{-- Live SEO score --}}
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800">Live SEO score</h3>
                            <span class="rounded-lg px-3 py-1 text-lg font-bold" :class="score>=80?'bg-green-100 text-green-700':(score>=50?'bg-amber-100 text-amber-700':'bg-red-100 text-red-700')" x-text="score + ' / 100'"></span>
                        </div>
                        <div class="mb-4 h-2 w-full rounded-full bg-slate-100"><div class="h-2 rounded-full transition-all" :class="score>=80?'bg-green-500':(score>=50?'bg-amber-500':'bg-red-500')" :style="`width:${score}%`"></div></div>
                        <ul class="space-y-1.5 text-sm">
                            <template x-for="c in checks" :key="c.label">
                                <li class="flex items-center gap-2" :class="c.ok?'text-slate-700':'text-slate-400'">
                                    <span x-text="c.ok?'✓':'○'" :class="c.ok?'text-green-600':'text-slate-300'"></span>
                                    <span x-text="c.label"></span>
                                </li>
                            </template>
                        </ul>
                        <p class="mt-3 text-xs text-slate-400">Live estimate — the authoritative score is recalculated on save. Aim for helpful content, never keyword stuffing.</p>
                    </div>
                </div>

                {{-- FAQ TAB --}}
                <div x-show="tab==='faqs'" x-cloak class="rounded-xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800">Frequently asked questions</h3>
                        <button type="button" @click="addFaq()" class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm text-white hover:bg-slate-700">+ Add FAQ</button>
                    </div>
                    <p class="mb-4 text-xs text-slate-400">Shown as an accordion on the blog, and output as FAQPage schema (only visible FAQs with a question and answer).</p>
                    <div class="space-y-4">
                        <template x-for="(faq, i) in faqs" :key="i">
                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-400" x-text="'#' + (i+1)"></span>
                                    <label class="ml-auto flex items-center gap-1 text-xs text-slate-500"><input type="checkbox" :name="`faqs[${i}][is_active]`" value="1" x-model="faq.is_active" class="rounded border-slate-300"> Visible</label>
                                    <button type="button" @click="moveFaq(i,-1)" class="rounded border border-slate-200 px-2 text-xs">↑</button>
                                    <button type="button" @click="moveFaq(i,1)" class="rounded border border-slate-200 px-2 text-xs">↓</button>
                                    <button type="button" @click="removeFaq(i)" class="rounded border border-red-200 px-2 text-xs text-red-600">✕</button>
                                </div>
                                <input :name="`faqs[${i}][question]`" x-model="faq.question" placeholder="Question" class="{{ $inp }} mb-2">
                                <textarea :name="`faqs[${i}][answer]`" x-model="faq.answer" rows="2" placeholder="Answer" class="{{ $inp }}"></textarea>
                            </div>
                        </template>
                        <p x-show="!faqs.length" class="py-6 text-center text-sm text-slate-400">No FAQs yet. Click “Add FAQ”.</p>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">{{ $post->exists ? 'Update Blog' : 'Create Blog' }}</button>
                    <div><label class="{{ $lbl }}">Status</label>
                        <select name="status" class="{{ $inp }}">
                            <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                        </select>
                    </div>
                    <div><label class="{{ $lbl }}">Publish date <span class="text-slate-400">(future = scheduled)</span></label><input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="{{ $inp }}"></div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Organize</h3>
                    <div><label class="{{ $lbl }}">Category</label>
                        <select name="category_id" class="{{ $inp }}">
                            <option value="">— None —</option>
                            @foreach ($categories as $cat)<option value="{{ $cat->id }}" @selected((int) old('category_id', $post->category_id) === $cat->id)>{{ $cat->path_label }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="{{ $lbl }}">…or new category</label><input name="new_category" value="{{ old('new_category') }}" placeholder="New category name" class="{{ $inp }}"></div>
                    <div><label class="{{ $lbl }}">Tags</label><input name="tags" value="{{ $tagsVal }}" placeholder="comma, separated" class="{{ $inp }}"></div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-3">
                    <h3 class="font-semibold text-slate-800">Featured image</h3>
                    @if ($post->featured_image)<img src="{{ $post->featured_image }}" alt="{{ $post->image_alt }}" class="mb-2 h-28 w-full rounded-lg border border-slate-200 object-cover">@endif
                    <input type="file" name="featured_image" accept="image/*" class="text-sm" @change="hasImage = ($event.target.files.length>0) || hasImage">
                    <p class="text-xs text-slate-400">A banner (1600×900) + thumbnail (640×400) WebP are generated automatically.</p>
                    @error('featured_image')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    <div><label class="{{ $lbl }}">Image ALT text</label><input name="image_alt" x-model="alt" placeholder="Describe the image" class="{{ $inp }}"></div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
function postForm(initial) {
    return {
        tab: 'content',
        faqs: initial.faqs || [],
        title: initial.title || '',
        alt: initial.alt || '',
        hasImage: !!initial.hasImage,
        seo: { title: initial.seoTitle || '', desc: initial.seoDesc || '', focus: initial.focus || '' },
        bodyHtml: '',
        bodyText: '',
        get effTitle() { return this.seo.title || this.title || ''; },
        get titleLen() { return this.effTitle.length; },
        get descLen() { return this.seo.desc.length; },
        get words() { return this.bodyText.trim() ? this.bodyText.trim().split(/\s+/).length : 0; },
        get checks() {
            const t = this.effTitle, f = (this.seo.focus || '').toLowerCase().trim();
            return [
                { label: 'URL / title set', weight: 5, ok: this.title.trim().length > 0 },
                { label: 'Title length 30–65 chars', weight: 10, ok: t.length >= 30 && t.length <= 65 },
                { label: 'Meta description 50–160 chars', weight: 15, ok: this.seo.desc.length >= 50 && this.seo.desc.length <= 160 },
                { label: 'Focus keyword set', weight: 10, ok: f !== '' },
                { label: 'Focus keyword in title', weight: 10, ok: f !== '' && t.toLowerCase().includes(f) },
                { label: 'Focus keyword in content', weight: 10, ok: f !== '' && this.bodyText.toLowerCase().includes(f) },
                { label: 'Featured image', weight: 10, ok: this.hasImage },
                { label: 'Image ALT text', weight: 5, ok: this.alt.trim().length > 0 },
                { label: 'Content ≥ 300 words', weight: 15, ok: this.words >= 300 },
                { label: 'Has an H2 subheading', weight: 10, ok: this.bodyHtml.toLowerCase().includes('<h2') },
            ];
        },
        get score() { return this.checks.filter(c => c.ok).reduce((s, c) => s + c.weight, 0); },
        addFaq() { this.faqs.push({ question: '', answer: '', is_active: true }); },
        removeFaq(i) { this.faqs.splice(i, 1); },
        moveFaq(i, d) { const j = i + d; if (j < 0 || j >= this.faqs.length) return; const a = this.faqs; [a[i], a[j]] = [a[j], a[i]]; },
        initEditor() {
            const self = this;
            jQuery(function ($) {
                $('#body').summernote({
                    height: 440,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'table', 'hr']],
                        ['misc', ['codeview', 'undo', 'redo']],
                    ],
                    callbacks: {
                        onImageUpload: function (files) { self.uploadImage(files[0], this); },
                        onChange: function (contents) { self.bodyHtml = contents; self.bodyText = $('<div>').html(contents).text(); },
                        onInit: function () { self.bodyHtml = $('#body').summernote('code'); self.bodyText = $('<div>').html(self.bodyHtml).text(); },
                    },
                });
            });
        },
        uploadImage(file, editor) {
            const fd = new FormData();
            fd.append('file', file); fd.append('_token', initial.csrf);
            fetch(initial.uploadUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(d => { if (d.url) jQuery(editor).summernote('insertImage', d.url); })
                .catch(() => alert('Image upload failed. Use JPG/PNG/WebP under 8MB.'));
        },
    };
}
</script>
@endpush
