{{-- Tab 1: Basic Info & Account --}}
<h4 class="mb-3">Account Information</h4>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
            @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
     <div class="col-md-6">
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" class="form-control-file @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture">
            @error('profile_picture')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            @if($user->profile_picture_path)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $user->profile_picture_path) }}" alt="Current Profile Picture" style="max-height: 80px; border-radius: var(--radius-sm);">
                <small class="form-text text-muted">Current picture. Upload a new one to replace it.</small>
            </div>
            @endif
        </div>
    </div>
</div>


<hr>
<h4 class="mb-3">Password Management</h4>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
    </div>
</div>

<hr>
<h4 class="mb-3">Roles & Verification</h4>
<div class="form-group">
    <label for="roles">Assign Roles</label>
    <select name="roles[]" id="roles" class="form-control select2-roles @error('roles') is-invalid @enderror" multiple="multiple" data-placeholder="Select Roles" style="width: 100%;">
        @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ (is_array(old('roles', $userRoles)) && in_array($role->name, old('roles', $userRoles))) ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
    @error('roles') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" value="1" {{ old('email_verified', optional($user->email_verified_at)->timestamp ? true : false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="email_verified">Mark email as verified</label>
    </div>
     @if($user->email_verified_at)
        <small class="form-text text-muted">Currently verified on {{ $user->email_verified_at->format('M d, Y H:i A') }}. Uncheck and save to un-verify.</small>
        {{-- This hidden input is used to detect if an admin explicitly wants to un-verify --}}
        <input type="hidden" name="email_verified_at_cleared" id="email_verified_at_cleared_input" value="0">
     @endif
</div>
