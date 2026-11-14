<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('role')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'is_active' => $request->boolean('is_active'),
            'email_verified_at' => now(),
        ]);
        ActivityLog::record('user.created', "User {$data['email']} created");

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user, 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateData($request, $user);

        // Guard: don't let the last super admin lose the role or be deactivated.
        if ($user->isSuperAdmin() && $this->isLastSuperAdmin($user)
            && (Role::find($data['role_id'])?->slug !== 'super-admin' || ! $request->boolean('is_active'))) {
            return back()->withErrors(['role_id' => 'You cannot remove or deactivate the last super admin.'])->withInput();
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'is_active' => $request->boolean('is_active'),
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        ActivityLog::record('user.updated', "User {$user->email} updated");

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }
        if ($user->isSuperAdmin() && $this->isLastSuperAdmin($user)) {
            return back()->withErrors(['user' => 'You cannot delete the last super admin.']);
        }

        $email = $user->email;
        $user->delete();
        ActivityLog::record('user.deleted', "User {$email} deleted");

        return back()->with('status', 'User deleted.');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->count() <= 1;
    }

    private function validateData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
    }
}
