@extends('layouts.admin.app')

@section('title', 'Edit Payment Account')
@section('page-title', 'Edit: ' . $paymentAccount->account_name)

@section('header-actions')
    <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Accounts
    </a>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.payment-accounts.update', $paymentAccount->id) }}" method="POST">
        @method('PUT')
        <div class="card-body">
            @include('admin.payment_accounts._form', ['paymentAccount' => $paymentAccount])
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Account</button>
        </div>
    </form>
</div>
@endsection
