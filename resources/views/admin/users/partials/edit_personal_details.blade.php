{{-- Tab 2: Personal Details (UserProfile Model) --}}
<h4 class="mb-3">Personal & Identity Information</h4>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="profile_father_name">Father's Name</label>
        <input type="text" class="form-control @error('profile.father_name') is-invalid @enderror" id="profile_father_name" name="profile[father_name]" value="{{ old('profile.father_name', $user->profile->father_name ?? '') }}">
        @error('profile.father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="profile_mother_name">Mother's Name</label>
        <input type="text" class="form-control @error('profile.mother_name') is-invalid @enderror" id="profile_mother_name" name="profile[mother_name]" value="{{ old('profile.mother_name', $user->profile->mother_name ?? '') }}">
        @error('profile.mother_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row">
    <div class="col-md-4 form-group">
        <label for="profile_date_of_birth">Date of Birth</label>
        <input type="text" class="form-control datepicker @error('profile.date_of_birth') is-invalid @enderror" id="profile_date_of_birth" name="profile[date_of_birth]" value="{{ old('profile.date_of_birth', optional(optional($user->profile)->date_of_birth)->format('Y-m-d') ?? '') }}" placeholder="YYYY-MM-DD">
        @error('profile.date_of_birth') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label for="profile_gender">Gender</label>
        <select class="form-control @error('profile.gender') is-invalid @enderror" id="profile_gender" name="profile[gender]">
            <option value="">Select Gender</option>
            <option value="male" {{ old('profile.gender', $user->profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('profile.gender', $user->profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('profile.gender', $user->profile->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('profile.gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label for="profile_blood_group">Blood Group</label>
        <input type="text" class="form-control @error('profile.blood_group') is-invalid @enderror" id="profile_blood_group" name="profile[blood_group]" value="{{ old('profile.blood_group', $user->profile->blood_group ?? '') }}" placeholder="e.g. A+">
        @error('profile.blood_group') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="profile_religion">Religion</label>
        <input type="text" class="form-control @error('profile.religion') is-invalid @enderror" id="profile_religion" name="profile[religion]" value="{{ old('profile.religion', $user->profile->religion ?? '') }}">
        @error('profile.religion') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="profile_whatsapp_number">WhatsApp Number</label>
        <input type="text" class="form-control @error('profile.whatsapp_number') is-invalid @enderror" id="profile_whatsapp_number" name="profile[whatsapp_number]" value="{{ old('profile.whatsapp_number', $user->profile->whatsapp_number ?? '') }}">
        @error('profile.whatsapp_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="profile_nid_number">NID Number</label>
        <input type="text" class="form-control @error('profile.nid_number') is-invalid @enderror" id="profile_nid_number" name="profile[nid_number]" value="{{ old('profile.nid_number', $user->profile->nid_number ?? '') }}">
        @error('profile.nid_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="profile_nid_file">NID Document (JPG, PNG, PDF)</label>
        <input type="file" class="form-control-file @error('profile.nid_file') is-invalid @enderror" id="profile_nid_file" name="profile[nid_file]">
        @error('profile.nid_file') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        @if(optional($user->profile)->nid_path)
            <small class="form-text text-muted">Current: <a href="{{ asset('storage/' . $user->profile->nid_path) }}" target="_blank">View NID</a></small>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="profile_passport_number">Passport Number</label>
        <input type="text" class="form-control @error('profile.passport_number') is-invalid @enderror" id="profile_passport_number" name="profile[passport_number]" value="{{ old('profile.passport_number', $user->profile->passport_number ?? '') }}">
        @error('profile.passport_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="profile_passport_file">Passport Document (JPG, PNG, PDF)</label>
        <input type="file" class="form-control-file @error('profile.passport_file') is-invalid @enderror" id="profile_passport_file" name="profile[passport_file]">
        @error('profile.passport_file') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
         @if(optional($user->profile)->passport_path)
            <small class="form-text text-muted">Current: <a href="{{ asset('storage/' . $user->profile->passport_path) }}" target="_blank">View Passport</a></small>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="profile_workplace_type">Workplace Type</label>
        <input type="text" class="form-control @error('profile.workplace_type') is-invalid @enderror" id="profile_workplace_type" name="profile[workplace_type]" value="{{ old('profile.workplace_type', $user->profile->workplace_type ?? '') }}">
        @error('profile.workplace_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>
<div class="form-group">
    <label for="profile_bio">Bio / About</label>
    <textarea class="form-control @error('profile.bio') is-invalid @enderror" id="profile_bio" name="profile[bio]" rows="4">{{ old('profile.bio', $user->profile->bio ?? '') }}</textarea>
    @error('profile.bio') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>
