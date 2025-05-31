<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-roles', ['only' => ['edit', 'update', 'updatePermissions']]);
        $this->middleware('permission:delete-roles', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Role::withCount('users', 'permissions');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        $roles = $query->latest()->paginate(10)->withQueryString();
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            // Group by first part of permission name, e.g., 'manage-users' -> 'users'
            return explode('-', $permission->name)[0];
        });
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        if ($role->name === 'Super-Admin' && !auth()->user()->hasRole('Super-Admin')) {
             abort(403, 'You are not authorized to edit the Super-Admin role.');
        }

        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode('-', $permission->name)[0];
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
         if ($role->name === 'Super-Admin' && !auth()->user()->hasRole('Super-Admin')) {
             abort(403, 'You cannot edit the Super-Admin role directly.');
         }
         if ($role->name === 'Super-Admin' && $request->name !== 'Super-Admin') {
             return back()->with('error', 'The Super-Admin role name cannot be changed.')->withInput();
         }


        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Do not allow changing name of Super-Admin role unless by another Super-Admin (or disallow completely)
        if ($role->name !== 'Super-Admin' || ($role->name === 'Super-Admin' && auth()->user()->hasRole('Super-Admin'))) {
            $role->name = $request->name;
            $role->save();
        }


        $selectedPermissions = $request->input('permissions', []);
        if ($role->name === 'Super-Admin') {
            // Super-Admin must have all permissions
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($selectedPermissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('edit-roles'); // Or a more specific 'assign-permissions' permission

        if ($role->name === 'Super-Admin') {
            return back()->with('error', 'Super-Admin permissions cannot be changed. It always has all permissions.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.edit', $role)->with('success', 'Role permissions updated successfully.');
    }


    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super-Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super-Admin role cannot be deleted.');
        }
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role. It is assigned to users.');
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
