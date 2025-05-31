@extends('layouts.admin.app')

@section('title', 'Manage Roles')
@section('page-title', 'Roles List')

@section('header-actions')
    @can('create-roles')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Role
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
             <div class="card-title">All Roles</div>
             <div class="card-tools">
                <form action="{{ route('admin.roles.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Search Role Name" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                     @if(request()->has('search'))
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($roles->isEmpty())
                <div class="alert alert-info m-3">No roles found.</div>
            @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Guard</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Created At</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>
                                    <a href="{{ route('admin.roles.show', $role->id) }}">{{ $role->name }}</a>
                                    @if($role->name === 'Super-Admin')
                                        <i class="fas fa-shield-alt text-success ml-1" title="Super Admin Role"></i>
                                    @endif
                                </td>
                                <td><span class="badge badge-secondary">{{ $role->guard_name }}</span></td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ $role->permissions_count }}</td>
                                <td>{{ $role->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        @can('view-roles')
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('edit-roles')
                                            @if($role->name !== 'Super-Admin' || Auth::user()->hasRole('Super-Admin'))
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        @endcan
                                        @can('delete-roles')
                                            @if($role->name !== 'Super-Admin')
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($roles->hasPages())
        <div class="card-footer clearfix">
             <div class="pagination-sm m-0 float-right">
                {{ $roles->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
