@extends('layouts.admin.app')

@section('title', 'Manage Permissions')
@section('page-title', 'Permissions List')

{{-- @section('header-actions')
    @can('create-permissions')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Permission (Not Recommended via UI)
        </a>
    @endcan
@endsection --}}

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">All Defined Permissions</div>
            <div class="card-tools">
                <form action="{{ route('admin.permissions.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Search Permission Name" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @if(request()->has('search'))
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($permissions->isEmpty())
                <div class="alert alert-info m-3">No permissions found or defined in the seeder.</div>
            @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Guard</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td><span class="badge badge-secondary">{{ $permission->guard_name }}</span></td>
                                <td>{{ $permission->created_at->format('d M, Y') }}</td>
                                <td>
                                     @can('view-permissions') {{-- Or a more specific 'view-permission-details' --}}
                                        <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn btn-xs btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    {{-- Edit/Delete for permissions usually not done via UI --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
         @if($permissions->hasPages())
        <div class="card-footer clearfix">
             <div class="pagination-sm m-0 float-right">
                {{ $permissions->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
