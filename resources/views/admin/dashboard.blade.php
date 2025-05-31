@extends('  .admin.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="container-fluid">
    <p>Welcome to the admin dashboard, {{ Auth::user()->name }}!</p>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ $totalUsers ?? 0 }}</p>
                    @can('manage-users')
                    <a href="{{-- route('admin.users.index') --}}" class="btn btn-primary">Manage Users</a>
                    @endcan
                </div>
            </div>
        </div>
        {{-- Add more cards for other stats as modules are built --}}
        {{--
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Applications</h5>
                    <p class="card-text display-4">0</p>
                    <a href="#" class="btn btn-warning">View Applications</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Trainings</h5>
                    <p class="card-text display-4">0</p>
                    <a href="#" class="btn btn-info">Manage Trainings</a>
                </div>
            </div>
        </div>
        --}}
    </div>

    {{-- Further dashboard content can go here --}}

</div>
@endsection

@push('styles')
{{-- Add any dashboard specific styles if needed --}}
<style>
    .card { margin-bottom: 20px; }
</style>
@endpush

@push('scripts')
{{-- Add any dashboard specific scripts if needed --}}
@endpush
