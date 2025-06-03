<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\User; // Import User model

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-users');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id; // Get user ID from route model binding
        $userBeingEdited = User::findOrFail($userId); // Get the user model being edited

        return [
            // User Table Fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users','phone_number')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'email_verified' => ['sometimes', 'boolean'],
            'email_verified_at_cleared' => ['sometimes', 'boolean'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // For admin uploading profile pic

            // UserProfile Table Fields (prefix with 'profile.')
            'profile.father_name' => ['nullable', 'string', 'max:255'],
            'profile.mother_name' => ['nullable', 'string', 'max:255'],
            'profile.date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'profile.blood_group' => ['nullable', 'string', 'max:5'],
            'profile.gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'profile.religion' => ['nullable', 'string', 'max:50'],
            'profile.whatsapp_number' => ['nullable', 'string', 'max:20', Rule::unique('user_profiles', 'whatsapp_number')->ignore($userBeingEdited->profile->id ?? null)],
            'profile.nid_number' => ['nullable', 'string', 'max:30', Rule::unique('user_profiles', 'nid_number')->ignore($userBeingEdited->profile->id ?? null)],
            'profile.nid_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'profile.passport_number' => ['nullable', 'string', 'max:30', Rule::unique('user_profiles', 'passport_number')->ignore($userBeingEdited->profile->id ?? null)],
            'profile.passport_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'profile.workplace_type' => ['nullable', 'string', 'max:100'],
            'profile.bio' => ['nullable', 'string', 'max:5000'],

            // UserAddresses Table Fields (as arrays)
            'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'exists:user_addresses,id'], // For updating existing
            'addresses.*.address_type' => ['required_with:addresses.*.address_line1', 'string', Rule::in(['permanent', 'present', 'work'])],
            'addresses.*.division_id' => ['nullable', 'exists:divisions,id'],
            'addresses.*.district_id' => ['nullable', 'exists:districts,id'],
            'addresses.*.upazila_id' => ['nullable', 'exists:upazilas,id'],
            'addresses.*.address_line1' => ['required_with:addresses.*.address_type', 'string', 'max:255'],
            'addresses.*.address_line2' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:10'],
            'addresses.*.is_primary' => ['nullable', 'boolean'],
            'addresses.*._delete' => ['nullable', 'boolean'], // For marking an address to be deleted

            // UserEducationRecords (as arrays)
            'education' => ['nullable', 'array'],
            'education.*.id' => ['nullable', 'exists:user_education_records,id'],
            'education.*.degree_level' => ['required_with:education.*.institution_name', 'string', 'max:100'],
            'education.*.degree_title' => ['required_with:education.*.institution_name', 'string', 'max:255'],
            'education.*.major_subject' => ['nullable', 'string', 'max:100'],
            'education.*.institution_name' => ['required_with:education.*.degree_level', 'string', 'max:255'],
            'education.*.graduation_year' => ['nullable', 'integer', 'digits:4', 'min:1950', 'max:'.(date('Y')+5)],
            'education.*.result_grade' => ['nullable', 'string', 'max:50'],
            'education.*._delete' => ['nullable', 'boolean'],

            // UserProfessionalExperiences (as arrays)
            'experience' => ['nullable', 'array'],
            'experience.*.id' => ['nullable', 'exists:user_professional_experiences,id'],
            'experience.*.designation' => ['required_with:experience.*.organization_name', 'string', 'max:255'],
            'experience.*.organization_name' => ['required_with:experience.*.designation', 'string', 'max:255'],
            'experience.*.start_date' => ['required_with:experience.*.designation', 'date'],
            'experience.*.end_date' => ['nullable', 'date', 'after_or_equal:experience.*.start_date'],
            'experience.*.is_current_job' => ['nullable', 'boolean'],
            'experience.*.responsibilities' => ['nullable', 'string', 'max:2000'],
            'experience.*._delete' => ['nullable', 'boolean'],

            // UserSocialLinks (as arrays)
            'social_links' => ['nullable', 'array'],
            'social_links.*.id' => ['nullable', 'exists:user_social_links,id'],
            'social_links.*.platform_name' => ['required_with:social_links.*.profile_url', 'string', 'max:50'],
            'social_links.*.profile_url' => ['required_with:social_links.*.platform_name', 'url', 'max:255'],
            'social_links.*._delete' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile.date_of_birth.before_or_equal' => 'The date of birth cannot be in the future.',
            'addresses.*.address_line1.required_with' => 'The address line 1 field is required when address type is present.',
            // Add more custom messages
        ];
    }
}
