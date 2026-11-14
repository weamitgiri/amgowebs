@extends('layouts.admin')
@section('title', $role->exists ? 'Edit Role' : 'New Role')

@php $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none'; @endphp

@section('content')
    <a href="{{ route('admin.roles.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to roles</a>

    <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="max-w-3xl">
        @csrf
        @if ($role->exists) @method('PUT') @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium text-slate-600">Role name *</label><input name="name" value="{{ old('name', $role->name) }}" required class="{{ $input }}"></div>
                <div><label class="mb-1 block text-sm font-medium text-slate-600">Description</label><input name="description" value="{{ old('description', $role->description) }}" class="{{ $input }}"></div>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6">
            <h3 class="mb-4 font-semibold text-slate-800">Permissions</h3>
            @if ($role->slug === 'super-admin')
                <p class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">Super Admin automatically has every permission.</p>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($permissionGroups as $group => $permissions)
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ ucfirst($group) }}</p>
                            <div class="space-y-1.5">
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', $assigned)))>
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6"><button class="rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">{{ $role->exists ? 'Update Role' : 'Create Role' }}</button></div>
    </form>
@endsection
