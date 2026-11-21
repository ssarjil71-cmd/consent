<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminPermissionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::with(['role', 'permissions'])->orderBy('name')->get();

        return view('admin.team_members.index', compact('admins'));
    }

    public function create()
    {
        AdminPermissionRegistry::sync();

        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.team_members.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? null,
        ]);

        $admin->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member created successfully.');
    }

    public function edit(Admin $team_member)
    {
        AdminPermissionRegistry::sync();

        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $team_member->load('permissions');

        return view('admin.team_members.edit', [
            'admin' => $team_member,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Admin $team_member)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $team_member->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $team_member->name = $data['name'];
        $team_member->email = $data['email'];
        $team_member->role_id = $data['role_id'] ?? null;

        if (!empty($data['password'])) {
            $team_member->password = Hash::make($data['password']);
        }

        $team_member->save();
        $team_member->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy(Admin $team_member)
    {
        if (auth('admin')->id() === $team_member->id) {
            return redirect()->route('admin.team-members.index')->with('error', 'You cannot delete your own account.');
        }

        $team_member->permissions()->detach();
        $team_member->delete();

        return redirect()->route('admin.team-members.index')->with('success', 'Team member deleted successfully.');
    }
}
