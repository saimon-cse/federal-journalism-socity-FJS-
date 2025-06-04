@extends('layouts.admin.app')

@section('title', 'Review Payment: #' . $payment->id)
@section('page-title', 'Review Payment Details')

@section('header-actions')
    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Payments
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Payment Information</h3></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Payment ID:</dt><dd class="col-sm-8">#{{ $payment->id }}</dd>
                    <dt class="col-sm-4">Payer Name:</dt><dd class="col-sm-8">{{ $payment->payer->name ?? 'N/A' }} (<a href="{{ route('admin.users.show', $payment->user_id) }}">View User</a>)</dd>
                    <dt class="col-sm-4">Payer Email:</dt><dd class="col-sm-8">{{ $payment->payer->email ?? 'N/A' }}</dd>
                    <hr class="col-12 my-2">
                    <dt class="col-sm-4">Purpose:</dt><dd class="col-sm-8">{{ $payment->purpose ?? 'N/A' }}</dd>
                    <dt class="col-sm-4">Payable Item:</dt>
                    <dd class="col-sm-8">
                        {{ Str::title(class_basename($payment->payable_type)) }} - ID: {{ $payment->payable_id }}
                        {{-- Add link to payable item if possible, e.g., link to training registration page --}}
                        @if($payment->payable_type === \App\Models\User::class && $payment->purpose === 'Membership Registration Fee')
                            (<a href="{{ route('admin.membership.applications.review', $payment->payable_id) }}">Review Membership App</a>)
                        @endif
                    </dd>
                    <dt class="col-sm-4">Amount Paid:</dt><dd class="col-sm-8">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</dd>
                    <dt class="col-sm-4">Paid To Account:</dt><dd class="col-sm-8">{{ $payment->paymentAccountReceivedIn->account_name ?? 'N/A' }} ({{ $payment->paymentAccountReceivedIn->account_identifier ?? '' }})</dd>
                    <dt class="col-sm-4">External Txn ID:</dt><dd class="col-sm-8">{{ $payment->external_transaction_id ?? 'N/A' }}</dd>
                    <dt class="col-sm-4">Payment Method Used:</dt><dd class="col-sm-8">{{ $payment->payment_method_used ?? 'N/A' }}</dd>
                    <dt class="col-sm-4">User Claimed Pay Date:</dt><dd class="col-sm-8">{{ $payment->payment_date->format('M d, Y H:i A') }}</dd>
                    <hr class="col-12 my-2">
                    <dt class="col-sm-4">Current Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}" style="font-size: 1rem;">
                            {{ Str::title(str_replace('_', ' ', $payment->status)) }}
                        </span>
                    </dd>
                    @if($payment->verified_by_user_id)
                        <dt class="col-sm-4">Last Action By:</dt>
                        <dd class="col-sm-8">{{ $payment->verifier->name ?? 'N/A' }} at {{ $payment->verified_at->format('M d, Y H:i') }}</dd>
                    @endif
                    @if($payment->verification_notes)
                        <dt class="col-sm-4">Last Notes:</dt>
                        <dd class="col-sm-8"><p style="white-space: pre-wrap;">{{ $payment->verification_notes }}</p></dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Payment Proof</h3></div>
            <div class="card-body text-center">
                @if($payment->payment_proof_path)
                    @php $fileExtension = pathinfo($payment->payment_proof_path, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                        <a href="{{ asset('storage/'.$payment->payment_proof_path) }}" target="_blank">
                            <img src="{{ asset('storage/'.$payment->payment_proof_path) }}" alt="Payment Proof for #{{ $payment->id }}" class="img-fluid" style="max-height: 400px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                        </a>
                         <p class="mt-2"><a href="{{ asset('storage/'.$payment->payment_proof_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Full Size</a></p>
                    @else
                        <p><a href="{{ asset('storage/'.$payment->payment_proof_path) }}" target="_blank" class="btn btn-lg btn-outline-secondary">
                            <i class="fas fa-file-alt fa-2x mr-2"></i> View/Download Proof ({{ strtoupper($fileExtension) }})
                        </a></p>
                    @endif
                @else
                    <div class="alert alert-warning">No payment proof was uploaded for this transaction.</div>
                @endif
            </div>
        </div>

        @if($payment->status == 'pending_verification')
        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Actions</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="mb-3" onsubmit="return confirm('Are you sure you want to VERIFY this payment?');">
                    @csrf
                    <div class="form-group">
                        <label for="verification_notes_approve">Verification Notes (Optional)</label>
                        <textarea name="verification_notes" id="verification_notes_approve" class="form-control" rows="2" placeholder="e.g., Verified against bank statement."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check-circle"></i> Mark as Verified</button>
                </form>
                <hr>
                <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectPaymentModal">
                    <i class="fas fa-times-circle"></i> Mark as Rejected
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="rejectPaymentModalLabel">Reject Payment #{{ $payment->id }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="rejection_reason_payment">Reason for Rejection <span class="text-danger">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason_payment" class="form-control @error('rejection_reason') is-invalid @enderror" rows="4" required placeholder="e.g., Transaction ID not found, Amount mismatch, Proof unclear.">{{ old('rejection_reason') }}</textarea>
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
