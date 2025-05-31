<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-permissions', ['only' => ['index', 'show']]);
        // Create, Edit, Delete for permissions are typically not exposed via UI
        // $this->middleware('permission:create-permissions', ['only' => ['create', 'store']]);
        // $this->middleware('permission:edit-permissions', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:delete-permissions', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Permission::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        $permissions = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function show(Permission $permission): View
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }

    // Typically, store, create, edit, update, destroy methods for permissions
    // are not implemented for UI management as permissions are code-defined.
    // If you need them, you can add them ensuring proper authorization.
}
