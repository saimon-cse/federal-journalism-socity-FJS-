<?php

namespace App\Http\Controllers\Frontend; // Assuming a Frontend namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Frontend\UserProfileUpdateRequest; // Create this
use App\Models\User;
use App\Models\Division;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // All methods require authentication
    }

    public function show(): View
    {
        $user = Auth::user()->load([
            'profile',
            'addresses.division', 'addresses.district', 'addresses.upazila',
            'educationRecords',
            'professionalExperiences',
            'socialLinks'
        ]);
        $divisions = Division::orderBy('name_en')->get();
        return view('frontend.profile.show', compact('user', 'divisions')); // Create this view
    }

    public function update(UserProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        // --- User Table Update (Limited fields user can update) ---
        $userData = [
            'name' => $validated['name'],
            // Email change might require re-verification, handle with care or disallow direct change here
            // 'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
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
        // ... (similar for passport_file)
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        // --- Sync Addresses, Education, Experience, Social Links ---
        // (Use the same syncHasMany helper method or replicate logic from UserController)
        $this->syncHasMany($user, 'addresses', $validated['addresses'] ?? []);
        $this->syncHasMany($user, 'educationRecords', $validated['education'] ?? []);
        $this->syncHasMany($user, 'professionalExperiences', $validated['experience'] ?? []);
        $this->syncHasMany($user, 'socialLinks', $validated['social_links'] ?? []);


        return redirect()->route('frontend.profile.show')->with('success', 'Your profile has been updated successfully!');
    }

    // Copy or move the syncHasMany helper method here or to a Trait
    protected function syncHasMany(User $user, string $relationName, array $submittedData): void
    {
        // ... (same implementation as in UserController)
        $existingIds = $user->{$relationName}->pluck('id')->toArray();
        $processedIds = [];

        foreach ($submittedData as $itemData) {
            if (!empty($itemData['_delete']) && !empty($itemData['id'])) {
                $user->{$relationName}()->find($itemData['id'])->delete();
                continue;
            }
            $relevantData = $itemData;
            unset($relevantData['id'], $relevantData['_delete']);
            if (empty(array_filter($relevantData))) {
                 if(isset($itemData['id']) && in_array($itemData['id'], $existingIds)) {
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
        $idsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($idsToDelete)) {
            $user->{$relationName}()->whereIn('id', $idsToDelete)->delete();
        }
    }
}
