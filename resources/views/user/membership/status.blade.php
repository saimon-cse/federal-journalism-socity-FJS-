@extends('layouts.admin.app') {{-- Or your user profile layout --}}

@section('title', 'My Membership Status')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            {{-- Optional: User Profile Sidebar --}}
            {{-- @include('user.profile.partials.sidebar') Create this if you have one --}}
        </div>
        <div class="col-md-9">
            <h2 class="mb-4">My Memberships</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
            @endif
             @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
            @endif


            @if ($latestApplication)
                <div class="card shadow-sm mb-4">
                    <div class="card-header @if($latestApplication->status == 'active') bg-success text-white @elseif($latestApplication->status == 'pending_payment' || $latestApplication->status == 'pending_application') bg-warning @else bg-light @endif">
                        <h5 class="mb-0">
                            Current Application/Membership Status:
                            <span class="badge membership-status {{ strtolower($latestApplication->status) }} float-right">
                                {{ ucfirst(str_replace('_', ' ', $latestApplication->status)) }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $latestApplication->membership_type)) }}</p>
                        @if($latestApplication->status == 'active')
                            <p><strong>Start Date:</strong> {{ $latestApplication->start_date ? $latestApplication->start_date->format('F d, Y') : 'N/A' }}</p>
                            <p><strong>End Date:</strong> {{ $latestApplication->end_date ? $latestApplication->end_date->format('F d, Y') : 'Lifetime' }}</p>
                            <p><strong>Last Payment:</strong> {{ $latestApplication->last_payment_date ? $latestApplication->last_payment_date->format('F d, Y') : 'N/A' }}</p>
                        @elseif($latestApplication->status == 'pending_payment')
                            <p class="text-danger">Your membership application is awaiting payment.</p>
                            <a href="{{ route('user.membership.payment.form', $latestApplication->id) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Proceed to Payment
                            </a>
                        @elseif($latestApplication->status == 'pending_application')
                            <p>Your application is under review by the administration. You will be notified once processed.</p>
                        @elseif($latestApplication->status == 'payment_failed' || $latestApplication->status == 'rejected')
                            <p class="text-danger">Your application could not be processed. Please check your email or contact support.</p>
                            @if($latestApplication->remarks)<p><strong>Admin Remarks:</strong> {{ $latestApplication->remarks }}</p>@endif
                            <a href="{{ route('user.membership.apply.form') }}" class="btn btn-outline-primary mt-2">Apply Again</a>
                        @elseif($latestApplication->status == 'expired')
                             <p class="text-warning">Your membership has expired.</p>
                             <a href="{{ route('user.membership.apply.form') }}" class="btn btn-primary mt-2">Renew Membership</a>
                        @endif

                        @if($latestApplication->remarks && !in_array($latestApplication->status, ['payment_failed', 'rejected']))
                            <p class="mt-3"><strong>Notes:</strong> {{ $latestApplication->remarks }}</p>
                        @endif

                        @if($latestApplication->payments->isNotEmpty())
                            <h6 class="mt-4">Payment Attempts:</h6>
                            <ul class="list-group list-group-flush">
                            @foreach($latestApplication->payments->sortByDesc('created_at') as $payment)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $payment->created_at->format('F d, Y h:i A') }} -
                                        <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                        </span>
                                        <br><small>TrxID: {{ $payment->manual_transaction_id_user ?? 'N/A' }} | Amount: BDT {{ number_format($payment->amount_paid, 2) }}</small>
                                        @if($payment->verification_remarks)
                                            <br><small class="text-muted"><i>Admin Note: {{ $payment->verification_remarks }}</i></small>
                                        @endif
                                    </div>
                                    @if($payment->hasMedia('payment_proofs'))
                                    <a href="{{ $payment->getFirstMediaUrl('payment_proofs') }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-receipt"></i> View Proof
                                    </a>
                                    @endif
                                </li>
                            @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    You have not applied for any memberships yet.
                    <a href="{{ route('user.membership.apply.form') }}" class="btn btn-primary float-right">Apply for Membership</a>
                </div>
            @endif


            @if($memberships->count() > 1) {{-- Show history if there's more than the latest one --}}
            <h4 class="mt-5 mb-3">Membership History</h4>
            <div class="list-group">
                @foreach ($memberships as $membership)
                    @if($latestApplication && $membership->id == $latestApplication->id) @continue @endif {{-- Skip the one already displayed --}}
                    <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ ucfirst(str_replace('_', ' ', $membership->membership_type)) }}</h5>
                            <small class="text-muted">{{ $membership->created_at->format('M d, Y') }}</small>
                        </div>
                        <p class="mb-1">
                            Status: <span class="badge membership-status {{ strtolower($membership->status) }}">{{ ucfirst(str_replace('_', ' ', $membership->status)) }}</span>
                        </p>
                        @if($membership->start_date) <small class="text-muted">Period: {{ $membership->start_date->format('M d, Y') }} - {{ $membership->end_date ? $membership->end_date->format('M d, Y') : 'Lifetime' }}</small> @endif
                         @if($membership->remarks)
                            <p class="mt-2 mb-0"><small><strong>Notes:</strong> {{ $membership->remarks }}</small></p>
                        @endif
                    </div>
                @endforeach
            </div>
             <div class="mt-3">
                {{ $memberships->links() }}
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
