@extends('layouts.admin')
@section('title', 'Roles & Permissions')

@section('content')
    @if ($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Roles control what each admin user can access.</p>
        <a href="{{ route('admin.roles.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Role</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($roles as $role)
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">{{ $role->name }}</h3>
                    @if ($role->is_system)<span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">System</span>@endif
                </div>
                <p class="mb-4 text-sm text-slate-500">{{ $role->description }}</p>
                <div class="mb-4 flex gap-4 text-xs text-slate-500">
                    <span><strong class="text-slate-800">{{ $role->users_count }}</strong> users</span>
                    <span><strong class="text-slate-800">{{ $role->slug === 'super-admin' ? 'All' : $role->permissions_count }}</strong> permissions</span>
                </div>
                <div class="flex gap-3 text-sm">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-cyan-600 hover:underline">Edit</a>
                    @unless ($role->is_system)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
@endsection
