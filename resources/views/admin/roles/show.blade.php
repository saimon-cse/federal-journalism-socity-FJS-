@extends('layouts.admin.app')

@section('title', 'Role Details: ' . $role->name)
@section('page-title', 'Role: ' . $role->name)

@section('header-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
    @can('edit-roles')
        {{-- Only allow editing Super-Admin if current user IS Super-Admin. Otherwise, prevent editing Super-Admin role. --}}
        @if($role->name !== 'Super-Admin' || Auth::user()->hasRole('Super-Admin'))
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit Role
            </a>
        @endif
    @endcan
@endsection

@section('styles')
<style>/* In your style.css if you want custom DL styling */
.dl-horizontal dt { /* If you add this class to dl */
    float: left;
    width: 160px; /* Adjust as needed */
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600; /* From your current dt style */
    color: var(--gray-600); /* Example color */
}
.dl-horizontal dd {
    margin-left: 180px; /* Adjust based on dt width + gap */
    margin-bottom: var(--spacing-2);
}
/* Clearfix for the dl if using floats */
.dl-horizontal::after {
    content: "";
    display: table;
    clear: both;
}</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Information</h3>
            </div>
            <div class="card-body">
                {{-- Using grid for dl is fine, ensure .col-sm-* are defined in your CSS grid system --}}
                <dl class="dl-horizontal">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $role->id }}</dd>

                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $role->name }}</dd>

                    <dt class="col-sm-4">Guard Name</dt>
                    <dd class="col-sm-8">{{ $role->guard_name }}</dd>

                    <dt class="col-sm-4">Users Assigned</dt>
                    <dd class="col-sm-8">{{ $role->users_count }}</dd> {{-- Use _count if you eager loaded it, or $paginatedUsers->total() from below --}}

                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8">{{ $role->created_at->format('M d, Y H:i A') }}</dd>

                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $role->updated_at->format('M d, Y H:i A') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permissions ({{ $role->permissions_count }})</h3> {{-- Use _count if eager loaded --}}
            </div>
            <div class="card-body p-0">
                @if($role->permissions->isEmpty())
                    <div class="alert alert-info m-3">This role has no permissions assigned.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($role->permissions->sortBy('name')->groupBy(function($permission) { return explode('-', $permission->name)[0]; }) as $group => $groupedPermissions)
                            <li class="list-group-item">
                                <strong class="d-block mb-2">{{ Str::title(str_replace('-', ' ', $group)) }}</strong>
                                <div class="d-flex flex-wrap" style="gap: var(--spacing-1);"> {{-- Using flexbox and gap for badges --}}
                                    @foreach($groupedPermissions as $permission)
                                        <span class="badge badge-info">{{ Str::title(str_replace('-', ' ', $permission->name)) }}</span>
                                    @endforeach
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Prepare paginated users once --}}
@php
    $paginatedUsers = $role->users()->paginate(10);
@endphp

<div class="card mt-4"> {{-- mt-4 should use var(--spacing-4) --}}
    <div class="card-header">
        <h3 class="card-title">Users with this Role ({{ $paginatedUsers->total() }})</h3> {{-- Use total from paginator --}}
    </div>
    <div class="card-body p-0">
        @if($paginatedUsers->isEmpty())
            <div class="alert alert-info m-3">No users are currently assigned to this role.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-sm"> {{-- table-sm should be styled --}}
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paginatedUsers as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @can('view-users')
                            {{-- Using btn-sm as btn-xs might not be defined. Define if needed. --}}
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info">View User</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($paginatedUsers->hasPages())
    <div class="card-footer"> {{-- clearfix might not be needed if pagination doesn't use floats --}}
        {{ $paginatedUsers->links('vendor.pagination.bootstrap-4-sm') }} {{-- Ensure this view exists and is styled --}}
    </div>
    @endif
</div>
@endsection
