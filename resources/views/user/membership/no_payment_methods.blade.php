@extends('layouts.admin.app')

@section('title', 'Membership Payment Unavailable')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Payment Information</h4>
                </div>
                <div class="card-body">
                    <p>You are applying for: <strong>{{ ucfirst(str_replace('_', ' ', $membership->membership_type)) }} Membership</strong>.</p>
                    <p>Amount Due: <strong class="text-danger">BDT {{ number_format($fee, 2) }}</strong></p>
                    <hr>
                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading">Payment Methods Unavailable</h5>
                        <p>We apologize, but there are currently no online or manual payment methods configured for membership fees.</p>
                        <p>Please contact our administration or support team for assistance with your payment.</p>
                        {{-- You can add contact information here --}}
                        {{-- <p>Contact: <a href="mailto:{{ Setting::get('support_email') }}">{{ Setting::get('support_email') }}</a> or call {{ Setting::get('support_phone') }}</p> --}}
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('user.membership.status') }}" class="btn btn-secondary">Back to Membership Status</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
