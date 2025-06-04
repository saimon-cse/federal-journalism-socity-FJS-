@extends('layouts.admin.app')

@section('title', 'All Payments')
@section('page-title', 'Payments Overview')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Records</h3>
            <div class="card-tools">
                {{-- Filter Form --}}
                <form method="GET" action="{{ route('admin.payments.index') }}" class="form-inline">
                    <div class="input-group input-group-sm mr-2" style="width: 180px;">
                        <input type="text" name="search_term" class="form-control float-right" placeholder="Search UUID/TrxID/User" value="{{ request('search_term') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            @foreach($paymentStatuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <div class="form-group mr-2">
                        <select name="payable_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach($payableTypes as $type)
                                <option value="{{ $type }}" {{ request('payable_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-default"><i class="fas fa-search"></i> Filter</button>
                     <a href="{{ route('admin.payments.index')}}" class="btn btn-sm btn-outline-secondary ml-2">Reset</a>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>UUID</th>
                            <th>Payer</th>
                            <th>Payable</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Method</th>
                            <th>User TrxID</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}">{{ $payment->payment_uuid }}</a>
                                </td>
                                <td>{{ $payment->user->name ?? $payment->payer_name ?? 'N/A' }}</td>
                                <td>
                                    {{ $payment->payable ? class_basename($payment->payable_type) . ' #'.$payment->payable->id : 'N/A' }}
                                    <br><small class="text-muted">{{ $payment->payable->title ?? $payment->payable->name ?? ($payment->payable->membership_type ?? '') }}</small>
                                </td>
                                <td>{{ number_format($payment->amount_due, 2) }} {{ $payment->currency_code }}</td>
                                <td>{{ number_format($payment->amount_paid, 2) }} {{ $payment->currency_code }}</td>
                                <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                <td>{{ $payment->manual_transaction_id_user ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                        {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                                    @if($payment->status === 'pending_manual_verification')
                                        @can('verify-manual-payments')
                                        {{-- Verification could be a modal on show page or direct action --}}
                                        {{-- Example: Link to show page where verification happens --}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
@endsection
