@extends('layouts.admin')
@section('title', 'Settings')

@php
    $label = fn ($key) => ucwords(str_replace(['_', 'seo ', 'social '], [' ', '', ''], $key));
    $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" novalidate
          x-data="{ tab: '{{ $groups->keys()->first() }}' }">
        @csrf @method('PUT')

        <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
            @foreach ($groups as $group => $items)
                <button type="button" @click="tab='{{ $group }}'"
                        :class="tab==='{{ $group }}' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600'"
                        class="rounded-lg px-4 py-2 text-sm font-medium">{{ $groupLabels[$group] ?? ucfirst($group) }}</button>
            @endforeach
        </div>

        @foreach ($groups as $group => $items)
            <div x-show="tab==='{{ $group }}'" class="mt-6 max-w-2xl space-y-5">
                @foreach ($items as $setting)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label($setting->key) }}</label>
                        @if ($setting->type === 'boolean')
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" @checked($setting->value)> Enabled
                            </label>
                        @elseif ($setting->type === 'textarea')
                            <textarea name="settings[{{ $setting->key }}]" rows="3" class="{{ $input }}">{{ $setting->value }}</textarea>
                        @elseif ($setting->type === 'image')
                            <div class="flex items-center gap-4">
                                @if ($setting->value)<img src="{{ $setting->value }}" alt="" class="h-12 w-12 rounded border border-slate-200 object-contain bg-slate-50">@endif
                                <input type="file" name="files[{{ $setting->key }}]" accept="image/*" class="text-sm">
                            </div>
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        @else
                            <input type="{{ $setting->type === 'email' ? 'email' : ($setting->type === 'url' ? 'url' : 'text') }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value === '#' ? '' : $setting->value }}" class="{{ $input }}" placeholder="{{ $setting->type === 'url' ? 'https://…' : '' }}">
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

        <div class="mt-8 border-t border-slate-200 pt-5">
            <button class="rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Save Settings</button>
        </div>
    </form>
@endsection
