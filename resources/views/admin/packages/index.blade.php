@extends('layouts.admin')
@section('title', 'Packages')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Manage your website packages, pricing and features.</p>
        @permission('packages.create')
            <a href="{{ route('admin.packages.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Package</a>
        @endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Package</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Features</th>
                    <th class="px-4 py-3">Featured</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($packages as $package)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $package->name }}</p>
                            <p class="text-xs text-slate-500">/{{ $package->slug }}</p>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-700">{{ $package->price_label }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $package->features_count }}</td>
                        <td class="px-4 py-3">
                            @permission('packages.edit')
                                <form method="POST" action="{{ route('admin.packages.toggle', $package) }}">
                                    @csrf <input type="hidden" name="field" value="is_featured">
                                    <button class="rounded-full px-2.5 py-1 text-xs font-medium {{ $package->is_featured ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-500' }}">{{ $package->is_featured ? 'Featured' : 'No' }}</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-500">{{ $package->is_featured ? 'Yes' : 'No' }}</span>
                            @endpermission
                        </td>
                        <td class="px-4 py-3">
                            @permission('packages.edit')
                                <form method="POST" action="{{ route('admin.packages.toggle', $package) }}">
                                    @csrf <input type="hidden" name="field" value="is_active">
                                    <button class="rounded-full px-2.5 py-1 text-xs font-medium {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-500' }}">{{ $package->is_active ? 'Active' : 'Inactive' }}</button>
                                </form>
                            @else
                                <span class="text-xs">{{ $package->is_active ? 'Active' : 'Inactive' }}</span>
                            @endpermission
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ url('/packages/'.$package->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>
                                @permission('packages.edit')<a href="{{ route('admin.packages.edit', $package) }}" class="text-cyan-600 hover:underline">Edit</a>@endpermission
                                @permission('packages.delete')
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete “{{ $package->name }}”?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">No packages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
