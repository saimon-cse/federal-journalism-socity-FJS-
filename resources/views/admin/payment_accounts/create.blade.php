@extends('layouts.admin.app')

@section('title', 'Create Payment Account')
@section('page-title', 'Create New Payment Account')

@section('header-actions')
    <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Account Details</h3>
        </div>
        <form action="{{ route('admin.payment-accounts.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_name">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name') }}" required>
                            @error('account_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_provider">Provider (e.g., Bkash, DBBL) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_provider') is-invalid @enderror" id="account_provider" name="account_provider" value="{{ old('account_provider') }}" required>
                            @error('account_provider')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_type">Account Type (e.g., Mobile Financial Service, Bank Account) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_type') is-invalid @enderror" id="account_type" name="account_type" value="{{ old('account_type') }}" required>
                            @error('account_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_identifier">Identifier (Number/Account No.)</label>
                            <input type="text" class="form-control @error('account_identifier') is-invalid @enderror" id="account_identifier" name="account_identifier" value="{{ old('account_identifier') }}">
                            @error('account_identifier')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_holder_name">Account Holder Name</label>
                            <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name') }}">
                            @error('account_holder_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
<div class="col-md-6">
        <div class="form-group">
            <label for="initial_balance">Initial Balance (BDT) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control @error('initial_balance') is-invalid @enderror" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', '0.00') }}" required>
            @error('initial_balance')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
                </div>

                <h5 class="mt-4 mb-3">Bank Account Details (Optional)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                            @error('bank_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="branch_name">Branch Name</label>
                            <input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" value="{{ old('branch_name') }}">
                            @error('branch_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="routing_number">Routing Number</label>
                            <input type="text" class="form-control @error('routing_number') is-invalid @enderror" id="routing_number" name="routing_number" value="{{ old('routing_number') }}">
                            @error('routing_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="manual_payment_instructions">Manual Payment Instructions (for users)</label>
                            <textarea class="form-control @error('manual_payment_instructions') is-invalid @enderror" id="manual_payment_instructions" name="manual_payment_instructions" rows="3">{{ old('manual_payment_instructions') }}</textarea>
                            @error('manual_payment_instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="allow_user_manual_payment_to" name="allow_user_manual_payment_to" value="1" {{ old('allow_user_manual_payment_to') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="allow_user_manual_payment_to">Allow Users to Manually Pay to this Account</label>
                            </div>
                             @error('allow_user_manual_payment_to') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                             @error('is_active') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Account
                </button>
                <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
