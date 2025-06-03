<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile; // Added
use App\Models\UserAddress; // Added
use App\Models\UserEducationRecord; // Added
use App\Models\UserProfessionalExperience; // Added
use App\Models\UserSocialLink; // Added
use App\Models\Division; // For address forms
use App\Models\District; // For address forms
use App\Models\Upazila;  // For address forms
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Added for file handling
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

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

   public function edit(User $user): View // Update this method too
    {
        if ($user->hasRole('Super-Admin') && !auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'You are not authorized to edit a Super-Admin user.');
        }

        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        $user->load([
            'profile',
            'addresses.division', 'addresses.district', 'addresses.upazila', // Eager load relations for addresses
            'educationRecords',
            'professionalExperiences',
            'socialLinks'
        ]);

        // For address dropdowns
        $divisions = Division::orderBy('name_en')->get();
        // Districts and Upazilas can be loaded via AJAX or passed if small sets

        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'divisions'));
    }


    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->hasRole('Super-Admin') && !auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'You are not authorized to edit a Super-Admin user.');
        }

        $validated = $request->validated();

        // --- User Table Update ---
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
        } elseif ($request->filled('email_verified_at_cleared') && $validated['email_verified_at_cleared']) {
            $userData['email_verified_at'] = null;
        }

        // Handle profile picture upload for User model
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture_path && Storage::disk('public')->exists($user->profile_picture_path)) {
                Storage::disk('public')->delete($user->profile_picture_path);
            }
            $userData['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        $user->update($userData);


        // --- UserProfile Update ---
        $profileData = $validated['profile'] ?? [];
        if ($request->hasFile('profile.nid_file')) {
            if ($user->profile && $user->profile->nid_path && Storage::disk('public')->exists($user->profile->nid_path)) {
                Storage::disk('public')->delete($user->profile->nid_path);
            }
            $profileData['nid_path'] = $request->file('profile.nid_file')->store('user_documents/nids', 'public');
        }
        if ($request->hasFile('profile.passport_file')) {
            if ($user->profile && $user->profile->passport_path && Storage::disk('public')->exists($user->profile->passport_path)) {
                Storage::disk('public')->delete($user->profile->passport_path);
            }
            $profileData['passport_path'] = $request->file('profile.passport_file')->store('user_documents/passports', 'public');
        }
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);


        // --- UserAddresses Update/Create/Delete ---
        if (isset($validated['addresses'])) {
            $existingAddressIds = $user->addresses->pluck('id')->toArray();
            $submittedAddressIds = [];

            foreach ($validated['addresses'] as $addressData) {
                if (!empty($addressData['_delete']) && !empty($addressData['id'])) {
                    UserAddress::find($addressData['id'])->delete(); // Or soft delete
                    continue;
                }
                if (empty(array_filter($addressData, fn($value) => $value !== null && $value !== false && $value !== ''))) { // Skip if all fields empty except id/_delete
                     if(isset($addressData['id']) && in_array($addressData['id'], $existingAddressIds)){ // If it's an existing empty submitted address, delete it.
                         UserAddress::find($addressData['id'])->delete();
                     }
                    continue;
                }


                unset($addressData['_delete']); // Remove delete flag before saving
                $address = $user->addresses()->updateOrCreate(
                    ['id' => $addressData['id'] ?? null], // Update if ID exists, else create
                    $addressData
                );
                $submittedAddressIds[] = $address->id;
            }
            // Delete addresses that were present before but not in this submission
            $addressesToDelete = array_diff($existingAddressIds, $submittedAddressIds);
            if (!empty($addressesToDelete)) {
                UserAddress::destroy($addressesToDelete);
            }
        } else {
            $user->addresses()->delete(); // Delete all if 'addresses' key is not present (meaning all were removed from form)
        }


        // --- UserEducationRecords Update/Create/Delete ---
        $this->syncHasMany($user, 'educationRecords', $validated['education'] ?? []);

        // --- UserProfessionalExperiences Update/Create/Delete ---
        $this->syncHasMany($user, 'professionalExperiences', $validated['experience'] ?? []);

        // --- UserSocialLinks Update/Create/Delete ---
        $this->syncHasMany($user, 'socialLinks', $validated['social_links'] ?? []);


        // --- Role Syncing (from previous implementation) ---
        if (!empty($validated['roles'])) {
            if ($user->hasRole('Super-Admin') && !in_array('Super-Admin', $validated['roles'])) {
                $superAdminCount = User::role('Super-Admin')->count();
                if ($superAdminCount <= 1 && $user->id === auth()->id()) { // If it's the only SA and it's themselves
                     return back()->with('error', 'You cannot remove the Super-Admin role from yourself as the last Super-Admin.')->withInput();
                }
                if ($superAdminCount <= 1) {
                    // Prevent removing from last super admin if not self
                     return back()->with('error', 'Cannot remove the Super-Admin role from the last Super-Admin user.')->withInput();
                }
            }
            $user->syncRoles($validated['roles']);
        } else { // No roles submitted
             if ($user->hasRole('Super-Admin')) {
                $superAdminCount = User::role('Super-Admin')->count();
                 if ($superAdminCount <= 1) {
                    return back()->with('error', 'Cannot remove all roles from the last Super-Admin user. Must retain Super-Admin role.')->withInput();
                 }
            }
            $user->syncRoles([]); // Detach all roles if not the last super admin
        }


        return redirect()->route('admin.users.index')->with('success', 'User profile updated successfully.');
    }

    /**
     * Helper function to sync hasMany relationships with create, update, delete logic.
     */
    protected function syncHasMany(User $user, string $relationName, array $submittedData): void
    {
        $existingIds = $user->{$relationName}->pluck('id')->toArray();
        $processedIds = [];

        foreach ($submittedData as $itemData) {
            if (!empty($itemData['_delete']) && !empty($itemData['id'])) {
                $user->{$relationName}()->find($itemData['id'])->delete(); // Or soft delete
                continue;
            }

            // Check if all relevant fields (excluding id and _delete) are empty
            $relevantData = $itemData;
            unset($relevantData['id'], $relevantData['_delete']);
            if (empty(array_filter($relevantData))) { // If all other fields are empty/null/false
                 if(isset($itemData['id']) && in_array($itemData['id'], $existingIds)) { // if it's an existing item submitted as empty
                     $user->{$relationName}()->find($itemData['id'])->delete();
                 }
                continue;
            }

            unset($itemData['_delete']);
            $item = $user->{$relationName}()->updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                $itemData
            );
            $processedIds[] = $item->id;
        }

        // Delete items that were not in the submission
        $idsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($idsToDelete)) {
            $user->{$relationName}()->whereIn('id', $idsToDelete)->delete();
        }
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
