@extends('layouts.admin')
@section('title', 'Media Library')

@section('content')
    @permission('media.manage')
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mb-6 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-4">
            @csrf
            <input type="file" name="file" accept="image/*" required class="text-sm">
            <input name="alt" placeholder="Alt text (for SEO & accessibility)" class="flex-1 min-w-[180px] rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Upload</button>
            @error('file')<p class="w-full text-xs text-red-600">{{ $message }}</p>@enderror
        </form>
    @endpermission

    @if ($media->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-12 text-center text-slate-400">No media uploaded yet.</div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($media as $item)
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white" x-data="{ edit: false }">
                    <div class="aspect-video bg-slate-100">
                        @if ($item->is_image)<img src="{{ $item->url }}" alt="{{ $item->alt }}" class="h-full w-full object-cover">@else<div class="grid h-full place-items-center text-slate-400">File</div>@endif
                    </div>
                    <div class="p-3">
                        <p class="truncate text-xs font-medium text-slate-700" title="{{ $item->filename }}">{{ $item->filename }}</p>
                        <p class="text-xs text-slate-400">{{ $item->width }}×{{ $item->height }} · {{ $item->human_size }}</p>
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <button type="button" @click="navigator.clipboard.writeText('{{ $item->url }}')" class="text-cyan-600 hover:underline">Copy URL</button>
                            @permission('media.manage')
                                <div class="flex gap-2">
                                    <button type="button" @click="edit=!edit" class="text-slate-500 hover:underline">Alt</button>
                                    <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Delete this file?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                                </div>
                            @endpermission
                        </div>
                        @permission('media.manage')
                            <form x-show="edit" method="POST" action="{{ route('admin.media.update', $item) }}" class="mt-2" style="display:none">
                                @csrf @method('PUT')
                                <input name="alt" value="{{ $item->alt }}" placeholder="Alt text" class="w-full rounded border border-slate-300 px-2 py-1 text-xs">
                                <button class="mt-1 w-full rounded bg-slate-900 py-1 text-xs text-white">Save alt</button>
                            </form>
                        @endpermission
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5">{{ $media->links() }}</div>
    @endif
@endsection
