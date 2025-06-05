@extends('layouts.admin.app')

@section('title', 'Membership Types')
@section('page-title', 'Membership Types')

@section('header-actions')
    @can('manage-memberships') {{-- Or specific 'manage-membership-types' --}}
        <a href="{{ route('admin.membership-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Type
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Membership Types</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Monthly Fee</th>
                            <th>Annual Fee</th>
                            <th>Duration</th>
                            <th>Recurring</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($membershipTypes as $index => $type)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->slug }}</td>
                                <td>{{ $type->monthly_amount ? number_format($type->monthly_amount, 2) . ' BDT' : 'N/A' }}</td>
                                <td>{{ $type->annual_amount ? number_format($type->annual_amount, 2) . ' BDT' : 'N/A' }}</td>
                                <td>{{ $type->membership_duration ?: 'N/A' }}</td>
                                <td>
                                    @if($type->is_recurring)
                                        <span class="badge badge-info">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage-memberships')
                                    <a href="{{ route('admin.membership-types.edit', $type->id) }}" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.membership-types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this type?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No membership types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $membershipTypes->links() }}
            </div>
        </div>
    </div>
@endsection
