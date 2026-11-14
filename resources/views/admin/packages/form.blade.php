@extends('layouts.admin')
@section('title', $package->exists ? 'Edit Package' : 'New Package')

@php
    $inc = old('included', $package->features->where('type', 'included')->pluck('label')->values()->all());
    $exc = old('excluded', $package->features->where('type', 'excluded')->pluck('label')->values()->all());
    $aLabels = old('addon_label', $package->features->where('type', 'addon')->pluck('label')->values()->all());
    $aPrices = old('addon_price', $package->features->where('type', 'addon')->pluck('addon_price')->values()->all());
    $addons = [];
    foreach ($aLabels as $i => $l) { $addons[] = ['label' => $l, 'price' => $aPrices[$i] ?? '']; }
    $seoTitle = old('seo_title', $package->seo?->title);
    $seoDesc = old('seo_description', $package->seo?->description);
    $inputClass = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
@endphp

@section('content')
    <a href="{{ route('admin.packages.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to packages</a>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Please fix the errors below.</div>
    @endif

    <form method="POST" action="{{ $package->exists ? route('admin.packages.update', $package) : route('admin.packages.store') }}"
          x-data="packageForm({ included: @js($inc ?: ['']), excluded: @js($exc ?: ['']), addons: @js($addons), customPricing: {{ old('is_custom_pricing', $package->is_custom_pricing) ? 'true' : 'false' }} })">
        @csrf
        @if ($package->exists) @method('PUT') @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- Basics --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Package details</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Name *</label><input name="name" value="{{ old('name', $package->name) }}" required class="{{ $inputClass }}">@error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Slug</label><input name="slug" value="{{ old('slug', $package->slug) }}" placeholder="auto from name" class="{{ $inputClass }}">@error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Short description</label><input name="short_description" value="{{ old('short_description', $package->short_description) }}" class="{{ $inputClass }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Full description</label><textarea name="description" rows="4" class="{{ $inputClass }}">{{ old('description', $package->description) }}</textarea></div>
                </div>

                {{-- Pricing --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Pricing</h3>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_custom_pricing" value="1" x-model="customPricing"> Custom pricing (hide price, show “Request a Quote”)</label>
                    <div class="grid gap-4 sm:grid-cols-3" x-show="!customPricing">
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Price (USD)</label><input type="number" step="0.01" name="price_usd" value="{{ old('price_usd', $package->price_usd) }}" class="{{ $inputClass }}">@error('price_usd')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Original price</label><input type="number" step="0.01" name="original_price" value="{{ old('original_price', $package->original_price) }}" class="{{ $inputClass }}"></div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Discount %</label><input type="number" name="discount_percent" value="{{ old('discount_percent', $package->discount_percent) }}" class="{{ $inputClass }}"></div>
                    </div>
                </div>

                {{-- Specs --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Specifications</h3>
                    <div class="grid gap-4 sm:grid-cols-4">
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Pages</label><input type="number" name="pages_included" value="{{ old('pages_included', $package->pages_included) }}" class="{{ $inputClass }}"></div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Delivery (days)</label><input type="number" name="delivery_days" value="{{ old('delivery_days', $package->delivery_days) }}" class="{{ $inputClass }}"></div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Support (months)</label><input type="number" name="support_months" value="{{ old('support_months', $package->support_months) }}" class="{{ $inputClass }}"></div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-600">Revisions</label><input type="number" name="revisions" value="{{ old('revisions', $package->revisions) }}" class="{{ $inputClass }}"></div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                    <h3 class="font-semibold text-slate-800">Features</h3>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-600">Included</label>
                        <template x-for="(item, i) in included" :key="'inc'+i">
                            <div class="mb-2 flex gap-2">
                                <input name="included[]" x-model="included[i]" class="{{ $inputClass }}" placeholder="e.g. Up to 5 pages">
                                <button type="button" @click="included.splice(i,1)" class="rounded-lg border border-slate-200 px-3 text-slate-400 hover:bg-slate-50">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="included.push('')" class="text-sm font-medium text-cyan-600 hover:underline">+ Add included</button>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-600">Excluded</label>
                        <template x-for="(item, i) in excluded" :key="'exc'+i">
                            <div class="mb-2 flex gap-2">
                                <input name="excluded[]" x-model="excluded[i]" class="{{ $inputClass }}" placeholder="e.g. Hosting (separate)">
                                <button type="button" @click="excluded.splice(i,1)" class="rounded-lg border border-slate-200 px-3 text-slate-400 hover:bg-slate-50">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="excluded.push('')" class="text-sm font-medium text-cyan-600 hover:underline">+ Add excluded</button>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-600">Optional add-ons (label + price)</label>
                        <template x-for="(item, i) in addons" :key="'add'+i">
                            <div class="mb-2 flex gap-2">
                                <input name="addon_label[]" x-model="item.label" class="{{ $inputClass }}" placeholder="e.g. Extra page">
                                <input name="addon_price[]" x-model="item.price" type="number" step="0.01" class="w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Price">
                                <button type="button" @click="addons.splice(i,1)" class="rounded-lg border border-slate-200 px-3 text-slate-400 hover:bg-slate-50">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="addons.push({label:'',price:''})" class="text-sm font-medium text-cyan-600 hover:underline">+ Add add-on</button>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">SEO</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">SEO title</label><input name="seo_title" value="{{ $seoTitle }}" class="{{ $inputClass }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Meta description</label><textarea name="seo_description" rows="2" class="{{ $inputClass }}">{{ $seoDesc }}</textarea></div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800">Publish</h3>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
                        <select name="status" class="{{ $inputClass }}">
                            <option value="published" @selected(old('status', $package->status) === 'published')>Published</option>
                            <option value="draft" @selected(old('status', $package->status) === 'draft')>Draft</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true))> Active</label>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $package->is_featured))> Featured (Most Popular)</label>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Display order</label><input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" class="{{ $inputClass }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">CTA label</label><input name="cta_label" value="{{ old('cta_label', $package->cta_label ?? 'Get Started') }}" class="{{ $inputClass }}"></div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-600">Icon name</label><input name="icon" value="{{ old('icon', $package->icon) }}" placeholder="rocket, box, crown…" class="{{ $inputClass }}"></div>
                </div>
                <button type="submit" class="w-full rounded-lg bg-slate-900 py-3 text-sm font-semibold text-white hover:bg-slate-700">{{ $package->exists ? 'Update Package' : 'Create Package' }}</button>
            </div>
        </div>
    </form>

    <script>
        function packageForm(init) {
            return {
                customPricing: init.customPricing,
                included: init.included.length ? init.included : [''],
                excluded: init.excluded.length ? init.excluded : [''],
                addons: init.addons || [],
            };
        }
    </script>
@endsection
