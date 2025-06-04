@extends('layouts.admin.app')

@section('title', 'Create Payment Method')
@section('page-title', 'Create New Payment Method')

@section('header-actions')
    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Method Details</h3>
        </div>
        <form action="{{ route('admin.payment-methods.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Method Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="method_key">Method Key (e.g., bkash_manual) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('method_key') is-invalid @enderror" id="method_key" name="method_key" value="{{ old('method_key') }}" placeholder="lowercase_underscore_separated" required>
                            @error('method_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="manual" {{ old('type') == 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="gateway" {{ old('type') == 'gateway' ? 'selected' : '' }}>Gateway</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="provider_name">Provider Name (e.g., Bkash, SSLCommerz)</label>
                            <input type="text" class="form-control @error('provider_name') is-invalid @enderror" id="provider_name" name="provider_name" value="{{ old('provider_name') }}">
                            @error('provider_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group" id="default_manual_account_group" style="{{ old('type') == 'manual' ? '' : 'display:none;' }}">
                    <label for="default_manual_account_id">Default Org. Account (for Manual type)</label>
                    <select class="form-select @error('default_manual_account_id') is-invalid @enderror" id="default_manual_account_id" name="default_manual_account_id">
                        <option value="">None</option>
                        @foreach($manualPaymentAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('default_manual_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->account_name }} ({{ $account->account_provider }})
                            </option>
                        @endforeach
                    </select>
                    @error('default_manual_account_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description / Instructions</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo_path">Logo Path/URL (Optional)</label>
                            <input type="text" class="form-control @error('logo_path') is-invalid @enderror" id="logo_path" name="logo_path" value="{{ old('logo_path') }}">
                            @error('logo_path')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                    @error('is_active') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Method
                </button>
                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const manualAccountGroup = document.getElementById('default_manual_account_group');
        const manualAccountSelect = document.getElementById('default_manual_account_id');

        function toggleManualAccountField() {
            if (typeSelect.value === 'manual') {
                manualAccountGroup.style.display = 'block';
            } else {
                manualAccountGroup.style.display = 'none';
                manualAccountSelect.value = ''; // Clear selection if not manual
            }
        }
        typeSelect.addEventListener('change', toggleManualAccountField);
        toggleManualAccountField(); // Initial call
    });
</script>
@endsection
