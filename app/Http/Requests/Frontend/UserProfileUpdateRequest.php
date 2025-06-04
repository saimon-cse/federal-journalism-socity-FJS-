<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = Auth::id();
        $user = Auth::user(); // Get the authenticated user model

        return [
            // User Table Fields (limited)
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)], // Email change more complex
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users','phone_number')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],

            // UserProfile Table Fields (prefix with 'profile.')
            'profile.father_name' => ['nullable', 'string', 'max:255'],
            'profile.mother_name' => ['nullable', 'string', 'max:255'],
            'profile.date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'profile.blood_group' => ['nullable', 'string', 'max:5'],
            'profile.gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'profile.religion' => ['nullable', 'string', 'max:50'],
            'profile.whatsapp_number' => ['nullable', 'string', 'max:20', Rule::unique('user_profiles', 'whatsapp_number')->ignore(optional($user->profile)->id)],
            'profile.nid_number' => ['nullable', 'string', 'max:30', Rule::unique('user_profiles', 'nid_number')->ignore(optional($user->profile)->id)],
            'profile.nid_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'profile.passport_number' => ['nullable', 'string', 'max:30', Rule::unique('user_profiles', 'passport_number')->ignore(optional($user->profile)->id)],
            'profile.passport_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'profile.workplace_type' => ['nullable', 'string', 'max:100'],
            'profile.bio' => ['nullable', 'string', 'max:5000'],

            // Addresses, Education, Experience, Social Links (same as admin update request)
             'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'exists:user_addresses,id,user_id,'.$userId], // Scope to user
            'addresses.*.address_type' => ['required_with:addresses.*.address_line1', 'string', Rule::in(['permanent', 'present', 'work'])],
            'addresses.*.division_id' => ['nullable', 'exists:divisions,id'],
            'addresses.*.district_id' => ['nullable', 'exists:districts,id'],
            'addresses.*.upazila_id' => ['nullable', 'exists:upazilas,id'],
            'addresses.*.address_line1' => ['required_with:addresses.*.address_type', 'string', 'max:255'],
            'addresses.*.address_line2' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:10'],
            'addresses.*.is_primary' => ['nullable', 'boolean'],
            'addresses.*._delete' => ['nullable', 'boolean'],

            'education' => ['nullable', 'array'],
            'education.*.id' => ['nullable', 'exists:user_education_records,id,user_id,'.$userId],
            'education.*.degree_level' => ['required_with:education.*.institution_name', 'string', 'max:100'],
             // ... (rest of education rules) ...

            'experience' => ['nullable', 'array'],
            'experience.*.id' => ['nullable', 'exists:user_professional_experiences,id,user_id,'.$userId],
            'experience.*.designation' => ['required_with:experience.*.organization_name', 'string', 'max:255'],
            // ... (rest of experience rules) ...

            'social_links' => ['nullable', 'array'],
            'social_links.*.id' => ['nullable', 'exists:user_social_links,id,user_id,'.$userId],
            'social_links.*.platform_name' => ['required_with:social_links.*.profile_url', 'string', 'max:50'],
            // ... (rest of social_links rules) ...
        ];
    }
}
