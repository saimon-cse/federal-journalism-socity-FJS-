@extends('layouts.admin.app')

@section('title', 'Transaction Categories')
@section('page-title', 'Financial Transaction Categories')

@section('header-actions')
    @can('manage-financial-categories')
        <a href="{{ route('admin.financial-transaction-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Transaction Categories</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Parent Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @if($category->type == 'income')
                                        <span class="badge badge-success">Income</span>
                                    @elseif($category->type == 'expense')
                                        <span class="badge badge-danger">Expense</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($category->type) }}</span>
                                    @endif
                                </td>
                                <td>{{ $category->parentCategory->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($category->description, 50) ?: 'N/A' }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage-financial-categories')
                                    <a href="{{ route('admin.financial-transaction-categories.edit', $category->id) }}" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.financial-transaction-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No transaction categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
