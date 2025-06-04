@extends('layouts.admin.app')

@section('title', 'Payment Verifications')
@section('page-title', 'Submitted Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">Payments List</div>
        <div class="card-tools">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm mr-2" style="width: 200px;">
                    <select name="status" class="form-control">
                        <option value="">All Visible Statuses</option>
                        <option value="pending_verification" {{ request('status', 'pending_verification') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Txn ID, Purpose, Payer" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                 @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="alert alert-info m-3">No payments found matching your criteria.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Payer</th>
                        <th>Purpose</th>
                        <th>Amount ({{ $payments->first()->currency ?? 'BDT' }})</th>
                        <th>Txn ID</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->payer->name ?? 'N/A' }} <br><small class="text-muted">{{ $payment->payer->email ?? '' }}</small></td>
                        <td>{{ $payment->purpose ?? Str::title(class_basename($payment->payable_type)) . ' ID: ' . $payment->payable_id }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->external_transaction_id ?? 'N/A' }}</td>
                        <td>
                            <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                {{ Str::title(str_replace('_', ' ', $payment->status)) }}
                            </span>
                        </td>
                        <td>{{ $payment->payment_date->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.payments.review', $payment->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-search-plus"></i> Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($payments->hasPages())
    <div class="card-footer clearfix">
        <div class="pagination-sm m-0 float-right">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
