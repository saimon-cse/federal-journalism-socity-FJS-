@extends('layouts.admin.app')

@section('title', 'Review Membership Application: ' . $user->name)
@section('page-title', 'Review Application')

@section('header-actions')
    <a href="{{ route('admin.membership.applications.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Applications
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Applicant Details</h3></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Full Name:</dt><dd class="col-sm-8">{{ $user->name }}</dd>
                    <dt class="col-sm-4">Email:</dt><dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4">Phone:</dt><dd class="col-sm-8">{{ $user->phone_number ?? 'N/A' }}</dd>
                    <dt class="col-sm-4">Application Status:</dt>
                    <dd class="col-sm-8">
                         <span class="badge status-badge {{ strtolower(str_replace('_', '-', $user->membership_application_status)) }}">
                            {{ Str::title(str_replace('_', ' ', $user->membership_application_status)) }}
                        </span>
                    </dd>
                    @if($user->profile)
                        <dt class="col-sm-4 mt-2">NID Number:</dt><dd class="col-sm-8 mt-2">{{ $user->profile->nid_number ?? 'N/A' }}</dd>
                        @if($user->profile->nid_path)
                        <dt class="col-sm-4">NID Document:</dt>
                        <dd class="col-sm-8"><a href="{{ asset('storage/'.$user->profile->nid_path) }}" target="_blank">View NID</a></dd>
                        @endif
                        <dt class="col-sm-4">Father's Name:</dt><dd class="col-sm-8">{{ $user->profile->father_name ?? 'N/A' }}</dd>
                    @endif
                    <dt class="col-sm-4 mt-2">Joined Site:</dt><dd class="col-sm-8 mt-2">{{ $user->created_at->format('M d, Y') }}</dd>
                </dl>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info mt-2">View Full User Profile</a>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Payment Details</h3></div>
            <div class="card-body">
                @if($membershipPayment)
                    <dl class="row">
                        <dt class="col-sm-5">Payment Amount:</dt><dd class="col-sm-7">{{ $membershipPayment->amount }} BDT</dd>
                        <dt class="col-sm-5">Transaction ID:</dt><dd class="col-sm-7">{{ $membershipPayment->external_transaction_id }}</dd>
                        <dt class="col-sm-5">Paid To Account:</dt><dd class="col-sm-7">{{ $membershipPayment->paymentAccountReceivedIn->account_name ?? 'N/A' }} ({{ $membershipPayment->paymentAccountReceivedIn->account_identifier ?? '' }})</dd>
                        <dt class="col-sm-5">Payment Date:</dt><dd class="col-sm-7">{{ $membershipPayment->payment_date->format('M d, Y H:i A') }}</dd>
                        <dt class="col-sm-5">Payment Status:</dt>
                        <dd class="col-sm-7">
                            <span class="badge status-badge {{ strtolower(str_replace('_', '-', $membershipPayment->status)) }}">
                                {{ Str::title(str_replace('_', ' ', $membershipPayment->status)) }}
                            </span>
                        </dd>
                        <dt class="col-sm-5">Payment Proof:</dt>
                        <dd class="col-sm-7">
                            @if($membershipPayment->payment_proof_path)
                                <a href="{{ asset('storage/'.$membershipPayment->payment_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-receipt"></i> View Proof
                                </a>
                            @else
                                No proof uploaded.
                            @endif
                        </dd>
                         @if($membershipPayment->verified_by_user_id)
                            <dt class="col-sm-5 mt-2">Verified By:</dt>
                            <dd class="col-sm-7 mt-2">{{ $membershipPayment->verifier->name ?? 'N/A' }} at {{ $membershipPayment->verified_at->format('M d, Y H:i') }}</dd>
                        @endif
                        @if($membershipPayment->verification_notes)
                            <dt class="col-sm-5 mt-2">Notes:</dt>
                            <dd class="col-sm-7 mt-2">{{ $membershipPayment->verification_notes }}</dd>
                        @endif
                    </dl>

                    @if($user->membership_application_status == 'pending_approval' && $membershipPayment->status == 'pending_verification')
                        <hr>
                        <h5 class="mb-3">Actions</h5>
                        <div class="d-flex justify-content-between">
                            <form action="{{ route('admin.membership.applications.approve', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to APPROVE this membership and verify the payment?');">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Approve Membership & Verify Payment</button>
                            </form>

                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectApplicationModal">
                                <i class="fas fa-times-circle"></i> Reject Application
                            </button>
                        </div>
                    @elseif($membershipPayment->status == 'verified' && $user->is_member)
                         <div class="alert alert-success mt-3">Payment verified and membership is active.</div>
                    @elseif($membershipPayment->status == 'rejected' || $user->membership_application_status == 'rejected')
                        <div class="alert alert-danger mt-3">This application or its payment has been rejected.</div>
                    @else
                         <div class="alert alert-warning mt-3">Application status is '{{ Str::title(str_replace('_', ' ', $user->membership_application_status)) }}' and Payment status is '{{ Str::title(str_replace('_', ' ', $membershipPayment->status)) }}'. Review any notes or previous actions.</div>
                    @endif

                @else
                    <div class="alert alert-warning">No membership fee payment record found for this application.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Application Modal -->
<div class="modal fade" id="rejectApplicationModal" tabindex="-1" role="dialog" aria-labelledby="rejectApplicationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.membership.applications.reject', $user->id) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="rejectApplicationModalLabel">Reject Membership Application for {{ $user->name }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="rejection_reason">Reason for Rejection <span class="text-danger">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="4" required>{{ old('rejection_reason') }}</textarea>
                @error('rejection_reason') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    // Ensure Bootstrap modal JS is loaded if not already handled globally by your script.js
    // If your custom script.js handles modals, this might not be needed.
    // $('#rejectApplicationModal').modal({ show: false }); // Initialize if not auto by data-toggle
</script>
@endsection
