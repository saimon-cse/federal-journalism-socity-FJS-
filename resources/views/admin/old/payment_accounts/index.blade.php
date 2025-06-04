@extends('layouts.admin.app')

@section('title', 'Payment Accounts')
@section('page-title', 'Manage Payment Accounts')

@section('header-actions')
    <a href="{{ route('admin.payment-accounts.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add New Account
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Organization's Payment Accounts</h3>
    </div>
    <div class="card-body p-0">
        @if($paymentAccounts->isEmpty())
            <div class="alert alert-info m-3">No payment accounts configured yet.</div>
        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Type</th>
                        <th>Identifier</th>
                        <th>Holder Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentAccounts as $account)
                    <tr>
                        <td>{{ $account->account_name }}</td>
                        <td>{{ $account->display_type }}</td>
                        <td>{{ $account->account_identifier }}</td>
                        <td>{{ $account->account_holder_name ?? 'N/A' }}</td>
                        <td>
                            @if($account->is_active)
                                <span class="badge status-badge active">Active</span>
                            @else
                                <span class="badge status-badge inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.payment-accounts.edit', $account->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.payment-accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($paymentAccounts->hasPages())
    <div class="card-footer clearfix">
        <div class="float-right">
            {{ $paymentAccounts->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
