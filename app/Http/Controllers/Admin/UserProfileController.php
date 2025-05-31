<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\ProfileUpdateRequest; // We'll create this

class UserProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse // Use a FormRequest
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle password update separately if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        } else {
            // Remove password from validated data if not being changed
            unset($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture_path);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture_path = $path;
        }


        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
