{{-- Assuming you have a @extends('layouts.frontend.app') --}}
@extends('layouts.frontend.app') {{-- REPLACE with your actual frontend layout --}}

@section('title', 'Apply for Membership')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Membership Application Form</h4>
                </div>
                <div class="card-body">
                    @if (empty($user->profile->nid_number) || empty($user->profile->father_name) /* add other essential checks */)
                        <div class="alert alert-warning">
                            <p><strong>Profile Incomplete!</strong></p>
                            <p>Please complete your essential profile information before applying for membership. This includes:</p>
                            <ul>
                                @if(empty($user->profile->nid_number)) <li>National ID (NID) Number & Document</li> @endif
                                @if(empty($user->profile->father_name)) <li>Father's Name</li> @endif
                                {{-- Add more checks --}}
                            </ul>
                            <a href="{{ route('frontend.profile.show') }}" class="btn btn-info btn-sm">Update My Profile</a>
                        </div>
                    @else
                        <p class="lead">Welcome, {{ $user->name }}! Please fill out the form below to apply for membership.</p>
                        <p>The current one-time membership registration fee is: <strong>{{ $membershipFee }} BDT</strong>.</p>
                        <hr>

                        <form action="{{ route('frontend.membership.apply.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Display User's NID and other relevant info for confirmation --}}
                            <div class="mb-3">
                                <h5>Confirm Your Details:</h5>
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>NID Number:</strong> {{ $user->profile->nid_number ?? 'Not Provided (Update Profile)' }}</p>
                                {{-- Add more fields to confirm if needed --}}
                                <p><small><a href="{{ route('frontend.profile.show') }}">Need to update these details?</a></small></p>
                            </div>
                            <hr>

                            {{-- If NID file needs to be uploaded specifically for application (optional) --}}
                            {{-- @if(empty($user->profile->nid_path))
                            <div class="form-group mb-3">
                                <label for="nid_file_application">Upload NID Document (PDF, JPG, PNG) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('nid_file_application') is-invalid @enderror" id="nid_file_application" name="nid_file_application" required>
                                @error('nid_file_application') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            @endif --}}


                            <h5 class="mt-4">Payment Information</h5>
                            <p>Please make a payment of <strong>{{ $membershipFee }} BDT</strong> to one of the following accounts and upload the proof.</p>

                            @if($paymentAccounts->isNotEmpty())
                                <div class="list-group mb-3">
                                    @foreach($paymentAccounts as $account)
                                        <div class="list-group-item">
                                            <h6 class="mb-1">{{ $account->account_name }} ({{ Str::title(str_replace('_', ' ', $account->account_type)) }})</h6>
                                            <p class="mb-1"><strong>Account/Number:</strong> {{ $account->account_identifier }}</p>
                                            @if($account->bank_name) <p class="mb-1"><strong>Bank:</strong> {{ $account->bank_name }} {{ $account->branch_name ? '('.$account->branch_name.')' : '' }}</p> @endif
                                            @if($account->instructions_for_payer) <small class="text-muted d-block">{{ $account->instructions_for_payer }}</small> @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">Payment accounts are not configured yet. Please contact support.</div>
                            @endif

                            <div class="form-group mb-3">
                                <label for="membership_fee_paid">Amount Paid (BDT) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('membership_fee_paid') is-invalid @enderror" id="membership_fee_paid" name="membership_fee_paid" value="{{ old('membership_fee_paid', $membershipFee) }}" step="0.01" required>
                                @error('membership_fee_paid') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="payment_account_id">Payment Made To <span class="text-danger">*</span></label>
                                <select class="form-control @error('payment_account_id') is-invalid @enderror" id="payment_account_id" name="payment_account_id" required>
                                    <option value="">Select Account Paid To</option>
                                    @foreach($paymentAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('payment_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_name }} ({{ $account->account_identifier }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="transaction_id">Transaction ID / Reference <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" required>
                                @error('transaction_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="payment_proof">Upload Payment Proof (Screenshot/Receipt) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" required>
                                @error('payment_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group form-check mb-3">
                                <input type="checkbox" class="form-check-input @error('agreed_to_terms') is-invalid @enderror" id="agreed_to_terms" name="agreed_to_terms" value="1" {{ old('agreed_to_terms') ? 'checked' : '' }} required>
                                <label class="form-check-label" for="agreed_to_terms">I agree to the <a href="#" target="_blank">terms and conditions</a> of the organization.</label>
                                @error('agreed_to_terms') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Submit Application</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles_frontend') {{-- Use a different stack name for frontend --}}
<style>
    /* Add any page-specific styles here if needed */
</style>
@endsection
