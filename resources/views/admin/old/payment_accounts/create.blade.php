@extends('layouts.admin.app')

@section('title', 'Add Payment Account')
@section('page-title', 'Create New Payment Account')

@section('header-actions')
    <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Accounts
    </a>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.payment-accounts.store') }}" method="POST">
        <div class="card-body">
            @include('admin.payment_accounts._form', ['paymentAccount' => new App\Models\PaymentAccount()])
        </div>
        <div class="card-footer text-right">
             <a href="{{ route('admin.payment-accounts.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Account</button>
        </div>
    </form>
</div>
@endsection
