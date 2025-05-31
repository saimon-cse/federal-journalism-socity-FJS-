@extends('layouts.admin.app')

@section('title', 'Create New User')
@section('page-title', 'Add New User')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                        @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="form-group">
                        <label for="father_name">Father's Name</label>
                        <input type="text" class="form-control @error('father_name') is-invalid @enderror" id="father_name" name="father_name" value="{{ old('father_name') }}">
                        @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="roles">Assign Roles</label>
                <select name="roles[]" id="roles" class="form-control select2 @error('roles') is-invalid @enderror" multiple="multiple" data-placeholder="Select Roles" style="width: 100%;">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('roles') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="email_verified">Mark email as verified</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e5e7eb; /* Match var(--border) */
            border-radius: 0.375rem; /* Match var(--radius) */
            min-height: calc(1.5em + 0.625rem * 2 + 2px); /* Match .form-control height */
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #4f46e5; /* Match var(--primary) */
            border-color: #4338ca; /* Match var(--primary-hover) */
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
                allowClear: true
            });
        });
    </script>
@endpush
