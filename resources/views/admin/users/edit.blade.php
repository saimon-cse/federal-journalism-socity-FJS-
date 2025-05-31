@extends('layouts.admin.app')

@section('title', 'Edit User: ' . $user->name)
@section('page-title', 'Edit User')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
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
                        <label for="father_name">Father's Name</label>
                        <input type="text" class="form-control @error('father_name') is-invalid @enderror" id="father_name" name="father_name" value="{{ old('father_name', $user->profile->father_name ?? '') }}">
                        @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

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

            <div class="form-group">
                <label for="roles">Assign Roles</label>
                <select name="roles[]" id="roles" class="form-control select2 @error('roles') is-invalid @enderror" multiple="multiple" data-placeholder="Select Roles" style="width: 100%;">
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
                    <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" value="1" {{ old('email_verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="email_verified">Mark email as verified</label>
                </div>
                 @if($user->email_verified_at)
                    <small class="form-text text-muted">Currently verified. Uncheck and save to un-verify (or provide a specific "unverify" button/action).</small>
                    <input type="hidden" name="email_verified_at_cleared" id="email_verified_at_cleared_input" value="0">
                 @endif
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            min-height: calc(1.5em + 0.625rem * 2 + 2px);
        }
         .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #4f46e5;
            border-color: #4338ca;
            color: white;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255,255,255,0.7);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true // This might not work well if roles are mandatory
            });

            // Handle email verification checkbox
            $('#email_verified').on('change', function() {
                if (!$(this).is(':checked') && {{ $user->email_verified_at ? 'true' : 'false' }}) {
                    $('#email_verified_at_cleared_input').val('1');
                } else {
                    $('#email_verified_at_cleared_input').val('0');
                }
            });
        });
    </script>
@endpush
