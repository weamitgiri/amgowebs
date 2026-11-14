<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users', 'permissions')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => new Role,
            'permissionGroups' => Permission::all()->groupBy('group'),
            'assigned' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);
        ActivityLog::record('role.created', "Role “{$role->name}” created");

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', [
            'role' => $role,
            'permissionGroups' => Permission::all()->groupBy('group'),
            'assigned' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);

        // Super admin always keeps every permission.
        if ($role->slug === 'super-admin') {
            $role->permissions()->sync(Permission::pluck('id'));
        } else {
            $role->permissions()->sync($data['permissions'] ?? []);
        }
        ActivityLog::record('role.updated', "Role “{$role->name}” updated");

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->withErrors(['role' => 'System roles cannot be deleted.']);
        }
        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Reassign users before deleting this role.']);
        }

        $role->delete();
        ActivityLog::record('role.deleted', "Role “{$role->name}” deleted");

        return back()->with('status', 'Role deleted.');
    }
}
