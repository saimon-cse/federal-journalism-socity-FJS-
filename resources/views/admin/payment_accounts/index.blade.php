@extends('layouts.admin.app')

@section('title', 'Payment Accounts')
@section('page-title', 'Payment Accounts')

@section('header-actions')
    @can('manage-payment-accounts')
        <a href="{{ route('admin.payment-accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Account
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Payment Accounts</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    {{-- ... inside the table ... --}}
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Account Name</th>
                            <th>Provider</th>
                            <th>Identifier</th>
                            <th>Initial Bal.</th> {{-- ADDED --}}
                            <th>Current Bal.</th> {{-- ADDED --}}
                            <th>Manual Allowed</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentAccounts as $index => $account)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $account->account_name }}</td>
                                <td>{{ $account->account_provider }}</td>
                                <td>{{ $account->account_identifier ?: 'N/A' }}</td>
                                <td>{{ number_format($account->initial_balance, 2) }}</td> {{-- ADDED --}}
                                <td><strong>{{ number_format($account->current_balance, 2) }}</strong></td>
                                {{-- ADDED --}}
                                <td>
                                    @if ($account->allow_user_manual_payment_to)
                                        <span class="badge badge-success">Allowed</span>
                                    @else
                                        <span class="badge badge-secondary">Not Allowed</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($account->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage-payment-accounts')
                                        <a href="{{ route('admin.payment-accounts.edit', $account->id) }}"
                                            class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.payment-accounts.destroy', $account->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No payment accounts found.</td> {{-- Adjusted colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- ... --}}
                </table>
            </div>
            <div class="mt-3">
                {{ $paymentAccounts->links() }}
            </div>
        </div>
    </div>
@endsection
