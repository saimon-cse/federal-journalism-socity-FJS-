<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\StoreUserRequest; // Create this
use App\Http\Requests\Admin\UpdateUserRequest; // Create this

class UserController extends Controller
{
    public function __construct()
    {
        // Apply permissions to controller methods if not done in routes
        $this->middleware('permission:view-users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-users', ['only' => ['edit', 'update', 'updateRoles']]);
        $this->middleware('permission:delete-users', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleName = $request->input('role');
            $query->whereHas('roles', function($q) use ($roleName){
                $q->where('name', $roleName);
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->pluck('name', 'name'); // For filtering

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $request->has('email_verified') ? now() : null,
        ]);

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Create UserProfile (basic)
        $user->profile()->create([
            'father_name' => $validated['father_name'] ?? null,
            // Add other profile fields if collected at creation
        ]);


        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load('roles', 'profile', 'addresses', 'educationRecords', 'professionalExperiences', 'socialLinks'); // Load all related data
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        // Prevent editing Super-Admin unless the current user is also Super-Admin and not editing themselves (or allow self-edit via profile page)
        if ($user->hasRole('Super-Admin') && !auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'You are not authorized to edit a Super-Admin user.');
        }
        if ($user->hasRole('Super-Admin') && auth()->user()->id !== $user->id && !auth()->user()->hasAllRoles(Role::where('name', 'Super-Admin')->first())) {
             // Prevent other Super-Admins from editing another Super-Admin unless specific logic allows
        }


        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        $user->load('profile'); // Eager load profile

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->hasRole('Super-Admin') && !auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'You are not authorized to edit a Super-Admin user.');
        }

        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        if ($request->has('email_verified')) {
            $userData['email_verified_at'] = now();
        } else if ($request->filled('email_verified_at_cleared')) { // Hidden field to clear verification
            $userData['email_verified_at'] = null;
        }


        $user->update($userData);

        // Update/Create UserProfile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'father_name' => $validated['father_name'] ?? $user->profile->father_name ?? null,
                // Add other profile fields
            ]
        );

        if (!empty($validated['roles'])) {
            // Prevent removing Super-Admin role from the last Super-Admin or from self if user is SA
            if ($user->hasRole('Super-Admin') && !in_array('Super-Admin', $validated['roles'])) {
                $superAdminCount = User::role('Super-Admin')->count();
                if ($superAdminCount <= 1) {
                    return back()->with('error', 'Cannot remove the Super-Admin role from the last Super-Admin user.')->withInput();
                }
                if (auth()->user()->id === $user->id) {
                    return back()->with('error', 'You cannot remove the Super-Admin role from yourself.')->withInput();
                }
            }
            $user->syncRoles($validated['roles']);
        } else {
            // If roles array is empty, detach all roles unless it's the last Super-Admin
            if ($user->hasRole('Super-Admin')) {
                $superAdminCount = User::role('Super-Admin')->count();
                 if ($superAdminCount <= 1 && auth()->user()->id === $user->id) {
                     // Allow removing other roles but keep Super-Admin
                     $user->syncRoles(['Super-Admin']);
                 } else if ($superAdminCount <= 1) {
                    return back()->with('error', 'Cannot remove all roles from the last Super-Admin user. Must retain Super-Admin role.')->withInput();
                 } else {
                    $user->syncRoles([]); // Detach all roles
                 }
            } else {
                $user->syncRoles([]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }


    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $this->authorize('edit-users'); // Or a more specific 'assign-roles' permission

        if ($user->hasRole('Super-Admin') && !auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'You are not authorized to change roles for a Super-Admin user.');
        }

        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $newRoles = $request->input('roles', []);

        // Prevent removing Super-Admin role from the last Super-Admin
        if ($user->hasRole('Super-Admin') && !in_array('Super-Admin', $newRoles)) {
            $superAdminCount = User::role('Super-Admin')->count();
            if ($superAdminCount <= 1) {
                 return back()->with('error', 'Cannot remove the Super-Admin role from the last Super-Admin user.');
            }
             if (auth()->user()->id === $user->id) {
                return back()->with('error', 'You cannot remove the Super-Admin role from yourself.');
            }
        }

        $user->syncRoles($newRoles);

        return redirect()->route('admin.users.edit', $user)->with('success', 'User roles updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }
        if ($user->hasRole('Super-Admin')) {
             $superAdminCount = User::role('Super-Admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.users.index')->with('error', 'Cannot delete the last Super-Admin user.');
            }
             if(!auth()->user()->hasRole('Super-Admin')) {
                 abort(403, 'You are not authorized to delete a Super-Admin user.');
             }
        }

        // Add any pre-deletion checks or related data handling here
        // For example, reassign tasks, delete related non-cascading data, etc.

        $user->profile()->delete(); // Manually delete if not set to cascade
        // Other related data like addresses, education etc. should have onDelete('cascade') in migrations or be handled here.
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
