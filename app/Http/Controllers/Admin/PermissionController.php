<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:permissions,slug'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);

        Permission::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:permissions,slug,' . $permission->id],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);

        $permission->update([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return redirect()->route('admin.permissions.index')->with('error', 'Cannot delete a permission assigned to roles.');
        }

        if ($permission->admins()->exists()) {
            return redirect()->route('admin.permissions.index')->with('error', 'Cannot delete a permission assigned directly to team members.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
