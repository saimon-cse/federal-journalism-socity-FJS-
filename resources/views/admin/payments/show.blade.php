@extends('layouts.admin.app')

@section('title', 'Payment Details - ' . $payment->payment_uuid)
@section('page-title', 'Payment Details')

@section('header-actions')
    <a href="{{ route('admin.payments.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to Payments
    </a>
@endsection
@section('styles')
<style>
    /* In your style.css if you want custom DL styling */
.dl-horizontal dt { /* If you add this class to dl */
    float: left;
    width: 160px; /* Adjust as needed */
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600; /* From your current dt style */
    color: var(--gray-600); /* Example color */
}
.dl-horizontal dd {
    margin-left: 180px; /* Adjust based on dt width + gap */
    margin-bottom: var(--spacing-2);
}
/* Clearfix for the dl if using floats */
.dl-horizontal::after {
    content: "";
    display: table;
    clear: both;
}
</style>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Information</h3>
                </div>
                <div class="card-body">
                    <dl class="dl-horizontal">
                        <dt class="col-sm-4">Payment UUID:</dt>
                        <dd class="col-sm-8">{{ $payment->payment_uuid }}</dd>

                        <dt class="col-sm-4">Payer:</dt>
                        <dd class="col-sm-8">
                            @if($payment->user)
                                <a href="{{-- route('admin.users.show', $payment->user_id) --}}">{{ $payment->user->name }} ({{ $payment->user->email }})</a>
                            @else
                                {{ $payment->payer_name ?? 'N/A' }} {{ $payment->payer_email ? '(' . $payment->payer_email . ')' : ''}}
                            @endif
                        </dd>

                        <dt class="col-sm-4">Payable Item:</dt>
                        <dd class="col-sm-8">
                             {{ $payment->payable ? class_basename($payment->payable_type) : 'N/A' }}
                            @if($payment->payable)
                                (#{{ $payment->payable->id }})
                                - {{ $payment->payable->title ?? $payment->payable->name ?? ($payment->payable->membership_type ?? 'Details unavailable') }}
                                {{-- Add link to payable item's admin page if exists --}}
                            @endif
                        </dd>

                        <dt class="col-sm-4">Amount Due:</dt>
                        <dd class="col-sm-8">{{ number_format($payment->amount_due, 2) }} {{ $payment->currency_code }}</dd>

                        <dt class="col-sm-4">Amount Paid:</dt>
                        <dd class="col-sm-8">{{ number_format($payment->amount_paid, 2) }} {{ $payment->currency_code }}</dd>

                        <dt class="col-sm-4">Payment Method:</dt>
                        <dd class="col-sm-8">{{ $payment->paymentMethod->name ?? 'N/A' }}</dd>

                        @if($payment->paymentMethod?->type === 'manual')
                            <dt class="col-sm-4">User's Transaction ID:</dt>
                            <dd class="col-sm-8">{{ $payment->manual_transaction_id_user ?? 'Not Provided' }}</dd>

                            <dt class="col-sm-4">User's Payment Datetime:</dt>
                            <dd class="col-sm-8">{{ $payment->manual_payment_datetime_user ? $payment->manual_payment_datetime_user->format('d M Y, h:i A') : 'Not Provided' }}</dd>

                            <dt class="col-sm-4">Paid to Account (User Claimed):</dt>
                            <dd class="col-sm-8">{{ $payment->manualPaymentToAccount->account_name ?? 'Not Specified' }}</dd>

                            <dt class="col-sm-4">Payment Proof:</dt>
                            <dd class="col-sm-8">

                                @if($payment->hasMedia('payment_proofs'))
                                    <a href="{{ $payment->getFirstMediaUrl('payment_proofs') }}" target="_blank">
                                        View Proof <i class="fas fa-external-link-alt fa-xs"></i>
                                        <img src="{{ $payment->getFirstMediaUrl('payment_proofs') }}" alt="Payment Proof" style="max-width: 200px; max-height: 200px; margin-top: 5px; border:1px solid #ddd;">
                                    </a>
                                @else
                                    No proof uploaded.
                                @endif
                            </dd>
                        @endif

                        @if($payment->paymentMethod?->type === 'gateway')
                            <dt class="col-sm-4">Gateway Name:</dt>
                            <dd class="col-sm-8">{{ $payment->gateway_name ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Gateway Transaction ID:</dt>
                            <dd class="col-sm-8">{{ $payment->gateway_transaction_id ?? 'N/A' }}</dd>
                        @endif


                        <dt class="col-sm-4">Current Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Submitted At:</dt>
                        <dd class="col-sm-8">{{ $payment->created_at->format('d M Y, h:i A') }}</dd>

                        @if($payment->verified_by_user_id)
                            <dt class="col-sm-4">Verified/Processed By:</dt>
                            <dd class="col-sm-8">{{ $payment->verifiedBy->name ?? 'System' }}</dd>
                            <dt class="col-sm-4">Verified/Processed At:</dt>
                            <dd class="col-sm-8">{{ $payment->verified_at ? $payment->verified_at->format('d M Y, h:i A') : 'N/A' }}</dd>
                        @endif

                        @if($payment->verification_remarks)
                            <dt class="col-sm-4">Verification Remarks:</dt>
                            <dd class="col-sm-8">{{ $payment->verification_remarks }}</dd>
                        @endif

                         @if($payment->notes)
                            <dt class="col-sm-4">Internal Notes:</dt>
                            <dd class="col-sm-8">{{ $payment->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        @if($payment->status === 'pending_manual_verification')
            @can('verify-manual-payments')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">Verification Action</h3>
                    </div>
                    <div class="card-body">
                        <p>Please verify this payment against your bank/MFS statement before taking action.</p>

                        <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="form-group">
                                <label for="verification_remarks_success">Verification Remarks (Optional)</label>
                                <textarea name="verification_remarks" id="verification_remarks_success" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check-circle"></i> Mark as Verified & Successful
                            </button>
                        </form>

                        <hr>

                        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="rejection_reason">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                                @error('rejection_reason') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-times-circle"></i> Reject Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        @endif
    </div>
@endsection
