@extends('layouts.admin.app')

@section('title', 'Payment Methods')
@section('page-title', 'Payment Methods')

@section('header-actions')
    @can('manage-payment-method-settings')
        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Method
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Payment Methods</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Key</th>
                            <th>Type</th>
                            <th>Provider</th>
                            <th>Default Manual Acct.</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentMethods as $index => $method)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $method->name }}</td>
                                <td><code>{{ $method->method_key }}</code></td>
                                <td><span class="badge badge-info">{{ ucfirst($method->type) }}</span></td>
                                <td>{{ $method->provider_name ?: 'N/A' }}</td>
                                <td>{{ $method->defaultManualAccount->account_name ?? 'N/A' }}</td>
                                <td>{{ $method->sort_order }}</td>
                                <td>
                                    @if($method->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage-payment-method-settings')
                                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this method?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No payment methods found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $paymentMethods->links() }}
            </div>
        </div>
    </div>
@endsection
