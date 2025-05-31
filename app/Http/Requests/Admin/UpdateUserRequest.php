<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-users');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id; // Get user ID from route model binding

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users','phone_number')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'email_verified' => ['sometimes', 'boolean'], // For checkbox to verify
            'email_verified_at_cleared' => ['sometimes', 'boolean'], // Hidden field if un-verifying
            'father_name' => ['nullable', 'string', 'max:255'],
            // Add other profile fields
        ];
    }
}
