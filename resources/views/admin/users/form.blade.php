@extends('layouts.admin')
@section('title', $user->exists ? 'Edit User' : 'New User')

@php $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none'; @endphp

@section('content')
    <a href="{{ route('admin.users.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to users</a>
    @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-xl">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Name *</label><input name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Email *</label><input name="email" type="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}"></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium text-slate-600">Password {{ $user->exists ? '(leave blank to keep)' : '*' }}</label><input name="password" type="password" {{ $user->exists ? '' : 'required' }} class="{{ $input }}"></div>
                <div><label class="mb-1 block text-sm font-medium text-slate-600">Confirm Password</label><input name="password_confirmation" type="password" class="{{ $input }}"></div>
            </div>
            <div><label class="mb-1 block text-sm font-medium text-slate-600">Role *</label>
                <select name="role_id" required class="{{ $input }}">
                    <option value="">Select role</option>
                    @foreach ($roles as $role)<option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === $role->id)>{{ $role->name }}</option>@endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))> Active (can log in)</label>
            <div class="border-t border-slate-100 pt-4"><button class="rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">{{ $user->exists ? 'Update User' : 'Create User' }}</button></div>
        </div>
    </form>
@endsection
