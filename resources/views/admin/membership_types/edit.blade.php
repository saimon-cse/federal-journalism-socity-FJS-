@extends('layouts.admin.app')

@section('title', 'Edit Membership Type')
@section('page-title')
    Edit Type: <span class="text-primary">{{ $membershipType->name }}</span>
@endsection

@section('header-actions')
    <a href="{{ route('admin.membership-types.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Membership Type Details</h3>
        </div>
        <form action="{{ route('admin.membership-types.update', $membershipType->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $membershipType->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $membershipType->description) }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="monthly_amount">Monthly Fee (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('monthly_amount') is-invalid @enderror" id="monthly_amount" name="monthly_amount" value="{{ old('monthly_amount', $membershipType->monthly_amount) }}">
                            <small class="form-text text-muted">Leave blank if not applicable.</small>
                            @error('monthly_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="annual_amount">Annual Fee (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('annual_amount') is-invalid @enderror" id="annual_amount" name="annual_amount" value="{{ old('annual_amount', $membershipType->annual_amount) }}">
                            <small class="form-text text-muted">Leave blank if not applicable.</small>
                            @error('annual_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                 <div class="form-group mb-3">
                    <label for="membership_duration">Membership Duration (e.g., "12 months", "Lifetime")</label>
                    <input type="text" class="form-control @error('membership_duration') is-invalid @enderror" id="membership_duration" name="membership_duration" value="{{ old('membership_duration', $membershipType->membership_duration) }}" placeholder="e.g., 12 months">
                    @error('membership_duration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring', $membershipType->is_recurring) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_recurring">Is Fee Recurring?</label>
                            </div>
                            @error('is_recurring') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $membershipType->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            @error('is_active') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Type</button>
                <a href="{{ route('admin.membership-types.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
