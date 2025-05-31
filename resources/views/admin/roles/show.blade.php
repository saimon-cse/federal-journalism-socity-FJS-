@extends('layouts.admin.app')

@section('title', 'Role Details: ' . $role->name)
@section('page-title', 'Role: ' . $role->name)

@section('header-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
    @can('edit-roles')
        @if($role->name !== 'Super-Admin' || Auth::user()->hasRole('Super-Admin'))
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-edit"></i> Edit Role
        </a>
        @endif
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $role->id }}</dd>

                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $role->name }}</dd>

                    <dt class="col-sm-4">Guard Name</dt>
                    <dd class="col-sm-8">{{ $role->guard_name }}</dd>

                    <dt class="col-sm-4">Users Assigned</dt>
                    <dd class="col-sm-8">{{ $role->users()->count() }}</dd>

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
                <h3 class="card-title">Permissions ({{ $role->permissions()->count() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($role->permissions->isEmpty())
                    <div class="alert alert-info m-3">This role has no permissions assigned.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($role->permissions->sortBy('name')->groupBy(function($permission) { return explode('-', $permission->name)[0]; }) as $group => $groupedPermissions)
                            <li class="list-group-item">
                                <strong class="d-block mb-2">{{ Str::title(str_replace('-', ' ', $group)) }}</strong>
                                @foreach($groupedPermissions as $permission)
                                    <span class="badge badge-info mr-1 mb-1">{{ Str::title(str_replace('-', ' ', $permission->name)) }}</span>
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Users with this Role ({{ $role->users()->count() }})</h3>
    </div>
    <div class="card-body p-0">
        @if($role->users->isEmpty())
            <div class="alert alert-info m-3">No users are currently assigned to this role.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($role->users()->paginate(10) as $user) {{-- Paginate users --}}
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @can('view-users')
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-xs btn-outline-info">View User</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($role->users()->paginate(10)->hasPages())
    <div class="card-footer clearfix">
        {{ $role->users()->paginate(10)->links('vendor.pagination.bootstrap-4-sm') }}
    </div>
    @endif
</div>
@endsection
