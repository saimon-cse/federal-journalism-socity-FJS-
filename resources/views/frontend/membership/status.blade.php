@extends('layouts.frontend.app') {{-- REPLACE with your actual frontend layout --}}

@section('title', 'Membership Application Status')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Your Membership Status</h4>
                </div>
                <div class="card-body">
                    <p class="lead">Hello, {{ $user->name }}!</p>

                    @if($user->is_member)
                        <div class="alert alert-success">
                            <h5 class="alert-heading">You are an Active Member!</h5>
                            <p>Welcome to the organization. Your membership is active since {{ optional($user->membership_start_date)->format('M d, Y') }}.</p>
                            <hr>
                            <p class="mb-0">Explore member benefits or visit your <a href="{{ route('dashboard') }}">dashboard</a>.</p>
                        </div>
                    @elseif($user->membership_application_status)
                        @switch($user->membership_application_status)
                            @case('pending_approval')
                                <div class="alert alert-warning">
                                    <h5 class="alert-heading">Application Submitted</h5>
                                    <p>Your membership application and payment proof have been submitted successfully and are currently pending verification and approval by our team.</p>
                                    <p>You will be notified once your application is processed. Thank you for your patience.</p>
                                </div>
                                @break
                            @case('pending_payment') {{-- If you have this pre-payment state --}}
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">Application Initiated - Payment Pending</h5>
                                    <p>Your application has been initiated. Please complete the payment of the membership fee and submit the proof to proceed.</p>
                                    <p><a href="{{ route('frontend.membership.apply.create') }}" class="btn btn-primary">Complete Payment & Submit Proof</a></p>
                                </div>
                                @break
                            @case('approved') {{-- This state should ideally transition to is_member=true --}}
                                <div class="alert alert-success">
                                    <h5 class="alert-heading">Application Approved!</h5>
                                    <p>Congratulations! Your membership application has been approved. You are now a member.</p>
                                     <p class="mb-0">Explore member benefits or visit your <a href="{{ route('dashboard') }}">dashboard</a>.</p>
                                </div>
                                @break
                            @case('rejected')
                                <div class="alert alert-danger">
                                    <h5 class="alert-heading">Application Rejected</h5>
                                    <p>We regret to inform you that your membership application could not be approved at this time.</p>
                                    @if($user->membership_rejection_reason)
                                        <p><strong>Reason:</strong> {{ $user->membership_rejection_reason }}</p>
                                    @endif
                                    <p>If you have any questions, please contact support.</p>
                                </div>
                                @break
                            @default
                                <div class="alert alert-secondary">
                                    <p>There is no active membership application found for your account.</p>
                                    <p><a href="{{ route('frontend.membership.apply.create') }}" class="btn btn-primary">Apply for Membership Now</a></p>
                                </div>
                        @endswitch
                    @else
                        <div class="alert alert-secondary">
                            <p>You are not currently a member and have no pending applications.</p>
                            <p><a href="{{ route('frontend.membership.apply.create') }}" class="btn btn-primary">Apply for Membership Now</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
