@extends('layouts.admin')
@section('title', 'Preview')

@section('content')
    <div x-data="{ w: '100%', label: 'Desktop' }">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-slate-500 hover:text-slate-800">← Back to edit</a>
            <div class="ml-auto flex gap-1 rounded-lg border border-slate-200 bg-white p-1">
                @foreach (['Desktop' => '100%', 'Tablet' => '820px', 'Mobile' => '390px'] as $name => $width)
                    <button type="button" @click="w='{{ $width }}'; label='{{ $name }}'" :class="label==='{{ $name }}' ? 'bg-slate-900 text-white' : 'text-slate-600'" class="rounded-md px-3 py-1.5 text-sm font-medium">{{ $name }}</button>
                @endforeach
            </div>
            <a href="{{ route('admin.posts.preview', $post) }}" target="_blank" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100">Open in new tab ↗</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Live device preview --}}
            <div class="lg:col-span-2">
                <div class="mx-auto overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition-all" :style="`max-width:${w}`">
                    <div class="flex items-center gap-1.5 border-b border-slate-200 bg-slate-100 px-3 py-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        <span class="ml-2 truncate text-xs text-slate-400">{{ $url }}</span>
                    </div>
                    <iframe src="{{ route('admin.posts.preview', $post) }}" class="h-[72vh] w-full" title="Preview"></iframe>
                </div>
            </div>

            {{-- Snippet + social previews --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Google result preview</h3>
                    <div class="font-arial">
                        <p class="truncate text-xs text-slate-600">{{ $url }}</p>
                        <p class="text-lg leading-tight text-[#1a0dab]">{{ \Illuminate\Support\Str::limit($seo['title'], 60) }}</p>
                        <p class="mt-0.5 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($seo['description'], 158) }}</p>
                    </div>
                    @if (\Illuminate\Support\Str::length($seo['title']) > 60)<p class="mt-2 text-xs text-amber-600">Title may be truncated in search (>60 chars).</p>@endif
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Social share preview</h3>
                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        @php $ogImg = $seo['og_image'] ?: $post->banner_image; @endphp
                        @if ($ogImg)
                            <img src="{{ $ogImg }}" alt="" class="aspect-[1.91/1] w-full object-cover">
                        @else
                            <div class="grid aspect-[1.91/1] w-full place-items-center bg-slate-100 text-xs text-slate-400">No OG image</div>
                        @endif
                        <div class="bg-slate-50 px-3 py-2">
                            <p class="text-[11px] uppercase text-slate-400">{{ parse_url(url('/'), PHP_URL_HOST) }}</p>
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $seo['og_title'] ?? $seo['title'] }}</p>
                            <p class="line-clamp-2 text-xs text-slate-500">{{ $seo['og_description'] ?? $seo['description'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
