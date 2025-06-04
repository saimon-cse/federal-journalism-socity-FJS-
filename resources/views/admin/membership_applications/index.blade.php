@extends('layouts.admin.app')

@section('title', 'Membership Applications')
@section('page-title', 'Pending Membership Applications')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">Applications List</div>
        <div class="card-tools">
            <form action="{{ route('admin.membership.applications.index') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm mr-2" style="width: 200px;">
                    <select name="status" class="form-control">
                        <option value="">All Visible Statuses</option>
                        <option value="pending_approval" {{ request('status', 'pending_approval') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Search Name or Email" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('admin.membership.applications.index') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        @if($applications->isEmpty())
            <div class="alert alert-info m-3">No pending membership applications found matching your criteria.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Application Status</th>
                        <th>Payment Status</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $applicant)
                    <tr>
                        <td>{{ $applicant->name }}</td>
                        <td>{{ $applicant->email }}</td>
                        <td>
                            <span class="badge status-badge {{ strtolower(str_replace('_', '-', $applicant->membership_application_status)) }}">
                                {{ Str::title(str_replace('_', ' ', $applicant->membership_application_status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $payment = $applicant->payments->first(); // Assumes latest relevant payment is loaded
                            @endphp
                            @if($payment)
                                <span class="badge status-badge {{ strtolower(str_replace('_', '-', $payment->status)) }}">
                                    {{ Str::title(str_replace('_', ' ', $payment->status)) }}
                                </span>
                                @if($payment->payment_proof_path)
                                 <a href="{{ asset('storage/'.$payment->payment_proof_path) }}" target="_blank" class="text-info ml-1" title="View Proof"><i class="fas fa-receipt"></i></a>
                                @endif
                            @else
                                <span class="badge badge-secondary">No Payment Record</span>
                            @endif
                        </td>
                        <td>{{ $applicant->updated_at->format('M d, Y H:i') }}</td> {{-- Or payment submitted_at --}}
                        <td>
                            <a href="{{ route('admin.membership.applications.review', $applicant->id) }}" class="btn btn-sm btn-info">
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
    @if($applications->hasPages())
    <div class="card-footer clearfix">
        <div class="pagination-sm m-0 float-right">
            {{ $applications->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
