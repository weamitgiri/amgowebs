@extends('layouts.admin')
@section('title', 'Users')

@section('content')
    @if ($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Manage admin users and their roles.</p>
        @permission('users.manage')<a href="{{ route('admin.users.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add User</a>@endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Last login</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $user->role?->name ?? 'No role' }}</span></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-500' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="px-4 py-3 text-right">
                            @permission('users.manage')
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-cyan-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->email }}?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                                </div>
                            @endpermission
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
