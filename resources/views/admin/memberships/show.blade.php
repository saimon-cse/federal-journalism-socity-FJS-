@extends('layouts.admin.app')

@section('title', 'Membership Details')
@section('page-title')
    Membership: <span class="text-primary">{{ $membership->membershipType->name ?? 'N/A' }}</span> for
    {{ $membership->user->name ?? 'N/A' }}
@endsection

@section('header-actions')
    <a href="{{ route('admin.memberships.index') }}" class="btn btn-light mr-2">
        <i class="fas fa-arrow-left"></i> Back to Memberships
    </a>
    @can('manage-memberships')
        <a href="{{ route('admin.memberships.edit', $membership->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Membership
        </a>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Membership Information</h3>
                </div>
                <div class="card-body">
                    <dl class="dl-horizontal">
                        <dt class="col-sm-4">Member:</dt>
                        <dd class="col-sm-8">
                            @if ($membership->user)
                                {{ $membership->user->name }} ({{ $membership->user->email }})
                                {{-- <a href="{{ route('admin.users.show', $membership->user_id) }}">[View Profile]</a> --}}
                            @else
                                N/A
                            @endif
                        </dd>

                        <dt class="col-sm-4">Membership Type:</dt>
                        <dd class="col-sm-8">{{ $membership->membershipType->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge membership-status {{ strtolower($membership->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $membership->status)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Start Date:</dt>
                        <dd class="col-sm-8">
                            {{ $membership->start_date ? $membership->start_date->format('F d, Y') : 'N/A' }}</dd>

                        <dt class="col-sm-4">End Date:</dt>
                        <dd class="col-sm-8">
                            {{ $membership->end_date ? $membership->end_date->format('F d, Y') : ($membership->membershipType && strtolower($membership->membershipType->membership_duration) == 'lifetime' ? 'Lifetime' : 'N/A') }}
                        </dd>

                        <dt class="col-sm-4">Last Payment Date:</dt>
                        <dd class="col-sm-8">
                            {{ $membership->last_payment_date ? $membership->last_payment_date->format('F d, Y H:i A') : 'N/A' }}
                        </dd>

                        <dt class="col-sm-4">Next Due Date:</dt>
                        <dd class="col-sm-8">
                            {{ $membership->next_due_date ? $membership->next_due_date->format('F d, Y') : 'N/A' }}</dd>

                        <dt class="col-sm-4">Application Date:</dt>
                        <dd class="col-sm-8">{{ $membership->created_at->format('F d, Y H:i A') }}</dd>

                        @if ($membership->approvedBy)
                            <dt class="col-sm-4">Approved/Processed By:</dt>
                            <dd class="col-sm-8">{{ $membership->approvedBy->name }}</dd>
                            <dt class="col-sm-4">Approved/Processed At:</dt>
                            <dd class="col-sm-8">
                                {{ $membership->approved_at ? $membership->approved_at->format('F d, Y H:i A') : 'N/A' }}
                            </dd>
                        @endif

                        @if ($membership->remarks)
                            <dt class="col-sm-4">Admin Remarks:</dt>
                            <dd class="col-sm-8">{{ $membership->remarks }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($membership->payments->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Payment History for this Membership</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>User TrxID</th>
                                        <th>Status</th>
                                        <th>Proof</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($membership->payments->sortByDesc('created_at') as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d M Y, H:i') }}</td>
                                            <td>{{ number_format($payment->amount_paid, 2) }}
                                                {{ $payment->currency_code }}</td>
                                            <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->manual_transaction_id_user ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($payment->hasMedia('payment_proofs'))
                                                    <a href="{{ $payment->getFirstMediaUrl('payment_proofs') }}"
                                                        target="_blank" class="btn btn-xs btn-outline-info"><i
                                                            class="fas fa-receipt"></i> View</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.payments.show', $payment->id) }}"
                                                    class="btn btn-xs btn-info" title="View Payment Details"><i
                                                        class="fas fa-eye"></i> Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-lg-5 col-md-12">
            @if (in_array($membership->status, ['pending_application', 'pending_payment']))
                @can('manage-memberships')
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Admin Actions</h3>
                        </div>
                        <div class="card-body">
                            @if ($membership->status == 'pending_payment')
                                <p>This membership application is awaiting payment verification or manual activation if payment
                                    was offline.</p>
                                <p>If a payment record exists below with status "Pending Manual Verification", please verify it
                                    from the <a
                                        href="{{ route('admin.payments.index', ['status' => 'pending_manual_verification']) }}">Payments
                                        Verification Queue</a>.</p>
                                <hr>
                                <p>If payment was confirmed offline/directly and no payment record submitted by user, you can
                                    manually activate:</p>
                                <form action="{{ route('admin.memberships.update', $membership->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to MANUALLY ACTIVATE this membership? This assumes payment is confirmed.');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="active">
                                    <input type="hidden" name="membership_type_id"
                                        value="{{ $membership->membership_type_id }}"> {{-- Keep current type --}}
                                    <div class="form-group">
                                        <label for="start_date_manual">Start Date (if activating)</label>
                                        <input type="date" name="start_date" id="start_date_manual"
                                            class="form-control form-control-sm"
                                            value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="remarks_manual_activate">Activation Remarks (Optional)</label>
                                        <textarea name="remarks" class="form-control form-control-sm" rows="2">{{ old('remarks', 'Manually activated by admin.') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check-circle"></i>
                                        Manually Activate</button>
                                </form>
                                <hr>
                            @endif

                            <form action="{{ route('admin.memberships.reject.application', $membership->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to REJECT this application?');">
                                @csrf
                                <div class="form-group">
                                    <label for="rejection_reason">Reason for Rejection <span
                                            class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-times-circle"></i>
                                    Reject Application</button>
                            </form>
                        </div>
                    </div>
                @endif
                @can('manage-memberships')
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">User Profile Snapshot</h3>
                        </div>
                        <div class="card-body">
                            @if ($membership->user && $membership->user->profile)
                                <p><strong>Full Name:</strong> {{ $membership->user->profile->first_name }}
                                    {{ $membership->user->profile->last_name }}</p>
                                <p><strong>Phone:</strong> {{ $membership->user->profile->phone_primary }}</p>
                                <p><strong>NID:</strong> {{ $membership->user->profile->nid_number ?? 'Not Provided' }}</p>
                                {{-- Add more profile fields as needed --}}
                            @elseif($membership->user)
                                <p>User profile not fully set up.</p>
                            @else
                                <p>User data not available.</p>
                            @endif
                        </div>
                    </div>
                @endcan
                @endif
            </div>
        </div>
    @endsection
