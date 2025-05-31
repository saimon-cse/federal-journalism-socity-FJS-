@extends('layouts.admin.app')

@section('title', 'Manage Users')
@section('page-title', 'Users List')

@section('header-actions')
    @can('create-users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New User
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                All Registered Users
            </div>
            <div class="card-tools">
                {{-- Search and Filter Form --}}
                <form action="{{ route('admin.users.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm mr-2" style="width: 200px;">
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            @foreach($roles as $roleValue => $roleName)
                                <option value="{{ $roleValue }}" {{ request('role') == $roleValue ? 'selected' : '' }}>
                                    {{ $roleName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Search Name or Email" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                     @if(request()->has('search') || request()->has('role'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="alert alert-info m-3">No users found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th>Verified</th>
                                <th>Joined</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_picture_path)
                                                <img src="{{ asset('storage/' . $user->profile_picture_path) }}" alt="{{ $user->name }}" class="img-circle img-size-32 mr-2" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                                            @else
                                                <span class="img-circle img-size-32 mr-2 d-flex align-items-center justify-content-center bg-secondary text-white" style="width:32px; height:32px; border-radius:50%;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            @endif
                                            <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number ?? '-' }}</td>
                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="badge badge-primary mr-1">{{ $role->name }}</span>
                                        @empty
                                            <span class="badge badge-secondary">No Roles</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge status-badge active">Verified</span>
                                        @else
                                            <span class="badge status-badge pending">Not Verified</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            @can('view-users')
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('edit-users')
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete-users')
                                                @if(Auth::id() !== $user->id && (!$user->hasRole('Super-Admin') || ($user->hasRole('Super-Admin') && \App\Models\User::role('Super-Admin')->count() > 1)))
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
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
        @if($users->hasPages())
        <div class="card-footer clearfix">
            <div class="pagination-sm m-0 float-right">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .card-tools .form-inline .form-control,
    .card-tools .form-inline .btn {
        height: calc(1.8125rem + 2px); /* Match Bootstrap 4 input-group-sm height */
        font-size: 0.875rem;
    }
    .img-size-32 { width: 32px; height: 32px; }
</style>
@endpush
