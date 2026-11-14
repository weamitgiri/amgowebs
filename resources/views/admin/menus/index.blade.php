@extends('layouts.admin')
@section('title', 'Navigation')

@php $input = 'rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none'; @endphp

@section('content')
    <p class="mb-5 text-sm text-slate-500">Manage the header and footer navigation links shown on the public site.</p>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach (['header' => $header, 'footer' => $footer] as $location => $items)
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-4 font-semibold capitalize text-slate-800">{{ $location }} Menu</h3>

                <div class="space-y-2">
                    @forelse ($items as $item)
                        <form method="POST" action="{{ route('admin.menus.update', $item) }}" class="flex flex-wrap items-center gap-2 rounded-lg bg-slate-50 p-2">
                            @csrf @method('PUT')
                            <input type="hidden" name="location" value="{{ $location }}">
                            <input name="label" value="{{ $item->label }}" class="{{ $input }} w-32" placeholder="Label">
                            <input name="url" value="{{ $item->url }}" class="{{ $input }} flex-1 min-w-[120px]" placeholder="/url">
                            <input name="sort_order" type="number" value="{{ $item->sort_order }}" class="{{ $input }} w-16" title="Order">
                            <label class="flex items-center gap-1 text-xs text-slate-500"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> on</label>
                            <button class="rounded bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-700">Save</button>
                            <button type="submit" form="del-{{ $item->id }}" onclick="return confirm('Remove “{{ $item->label }}”?')" class="rounded border border-slate-200 px-2.5 py-1.5 text-xs text-red-600 hover:bg-red-50">&times;</button>
                        </form>
                    @empty
                        <p class="text-sm text-slate-400">No items yet.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('admin.menus.store') }}" class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    <input type="hidden" name="location" value="{{ $location }}">
                    <input name="label" class="{{ $input }} w-32" placeholder="New label" required>
                    <input name="url" class="{{ $input }} flex-1 min-w-[120px]" placeholder="/url" required>
                    <input name="sort_order" type="number" value="{{ $items->count() }}" class="{{ $input }} w-16">
                    <input type="hidden" name="is_active" value="1">
                    <button class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">+ Add</button>
                </form>
            </div>
        @endforeach
    </div>

    @foreach (['header' => $header, 'footer' => $footer] as $location => $items)
        @foreach ($items as $item)
            <form method="POST" action="{{ route('admin.menus.destroy', $item) }}" id="del-{{ $item->id }}" class="hidden">@csrf @method('DELETE')</form>
        @endforeach
    @endforeach
@endsection
