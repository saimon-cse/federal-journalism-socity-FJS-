@extends('layouts.admin.app')

@section('title', 'Permission Details: ' . $permission->name)
@section('page-title', 'Permission: ' . $permission->name)

@section('header-actions')
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Permissions
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permission Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $permission->id }}</dd>

                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $permission->name }}</dd>

                    <dt class="col-sm-4">Guard Name</dt>
                    <dd class="col-sm-8">{{ $permission->guard_name }}</dd>

                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8">{{ $permission->created_at->format('M d, Y H:i A') }}</dd>

                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $permission->updated_at->format('M d, Y H:i A') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roles with this Permission ({{ $permission->roles()->count() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($permission->roles->isEmpty())
                    <div class="alert alert-info m-3">This permission is not assigned to any roles.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($permission->roles->sortBy('name') as $role)
                            <li class="list-group-item">
                                <a href="{{ route('admin.roles.show', $role->id) }}">{{ $role->name }}</a>
                                 <span class="badge badge-secondary float-right">{{ $role->guard_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
