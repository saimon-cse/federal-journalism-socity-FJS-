@extends('layouts.admin.app')

@section('title', 'Manage Memberships')
@section('page-title', 'Membership Applications & Records')

@section('header-actions')
    {{-- Optionally, a button to manually create a membership if needed, though typical flow is user application --}}
    {{-- @can('manage-memberships')
        <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Membership Record
        </a>
    @endcan --}}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Membership List</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.memberships.index') }}" class="form-inline">
                    <div class="input-group input-group-sm mr-2" style="width: 200px;">
                        <input type="text" name="user_search" class="form-control float-right" placeholder="Search User Name/Email" value="{{ request('user_search') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach($types as $typeKey) {{-- Assuming $types is array of slugs/keys from MembershipTypeSeeder or similar --}}
                                <option value="{{ $typeKey }}" {{ request('type') == $typeKey ? 'selected' : '' }}>
                                    {{-- You might need to fetch MembershipType models to display names if $types is just keys --}}
                                    {{ Str::title(str_replace('_', ' ', $typeKey)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-default"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('admin.memberships.index')}}" class="btn btn-sm btn-outline-secondary ml-2">Reset</a>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Membership Type</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Last Payment</th>
                            <th>Applied On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $membership)
                            <tr>
                                <td>
                                    @if($membership->user)
                                        <a href="#">{{-- route('admin.users.show', $membership->user_id) --}} {{ $membership->user->name }}</a>
                                        <br><small>{{ $membership->user->email }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $membership->membershipType->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge membership-status {{ strtolower($membership->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $membership->status)) }}
                                    </span>
                                </td>
                                <td>{{ $membership->start_date ? $membership->start_date->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $membership->end_date ? $membership->end_date->format('d M Y') : ($membership->membershipType && strtolower($membership->membershipType->membership_duration) == 'lifetime' ? 'Lifetime' : 'N/A') }}</td>
                                <td>{{ $membership->last_payment_date ? $membership->last_payment_date->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $membership->created_at->format('d M Y') }}</td>
                                <td>
                                    @can('view-memberships')
                                        <a href="{{ route('admin.memberships.show', $membership->id) }}" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                                    @endcan
                                    @can('manage-memberships')
                                        <a href="{{ route('admin.memberships.edit', $membership->id) }}" class="btn btn-sm btn-warning" title="Edit Status/Dates"><i class="fas fa-edit"></i></a>
                                        @if(in_array($membership->status, ['pending_application', 'pending_payment']))
                                            {{-- Reject action might be on show page for more context --}}
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No membership records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $memberships->links() }}
            </div>
        </div>
    </div>
@endsection
