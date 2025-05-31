show.blade.php@extends('layouts.admin.app')

@section('title', 'User Details: ' . $user->name)
@section('page-title', 'User Profile')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
    @can('edit-users')
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit"></i> Edit User
    </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Image Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                @if($user->profile_picture_path)
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ asset('storage/' . $user->profile_picture_path) }}"
                         alt="{{ $user->name }} profile picture"
                         style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <span class="profile-user-img img-fluid img-circle d-flex align-items-center justify-content-center bg-secondary text-white mx-auto"
                          style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                @endif
                <h3 class="profile-username text-center mt-2">{{ $user->name }}</h3>
                <p class="text-muted text-center">
                    @forelse ($user->roles as $role)
                        {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                    @empty
                        No Role Assigned
                    @endforelse
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right">{{ $user->phone_number ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Joined</b> <a class="float-right">{{ $user->created_at->format('M d, Y') }}</a>
                    </li>
                     <li class="list-group-item">
                        <b>Email Verified</b>
                        <a class="float-right">
                             @if ($user->email_verified_at)
                                <span class="badge status-badge active">Verified</span>
                                ({{ $user->email_verified_at->format('M d, Y H:i') }})
                            @else
                                <span class="badge status-badge pending">Not Verified</span>
                            @endif
                        </a>
                    </li>
                </ul>
                @if(Auth::id() !== $user->id && Auth::user()->can('delete-users') && (!$user->hasRole('Super-Admin') || ($user->hasRole('Super-Admin') && \App\Models\User::role('Super-Admin')->count() > 1)))
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block"><b>Delete User</b></button>
                    </form>
                @endif
            </div>
        </div>

        <!-- About Me Card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">About {{ $user->name }}</h3>
            </div>
            <div class="card-body">
                @if($user->profile)
                    <strong><i class="fas fa-user-tie mr-1"></i> Father's Name</strong>
                    <p class="text-muted">{{ $user->profile->father_name ?? 'N/A' }}</p>
                    <hr>
                    <strong><i class="fas fa-calendar-alt mr-1"></i> Date of Birth</strong>
                    <p class="text-muted">{{ $user->profile->date_of_birth ? \Carbon\Carbon::parse($user->profile->date_of_birth)->format('M d, Y') : 'N/A' }}</p>
                    <hr>
                    <strong><i class="fas fa-venus-mars mr-1"></i> Gender</strong>
                    <p class="text-muted">{{ Str::title($user->profile->gender) ?? 'N/A' }}</p>
                     <hr>
                    <strong><i class="fas fa-tint mr-1"></i> Blood Group</strong>
                    <p class="text-muted">{{ $user->profile->blood_group ?? 'N/A' }}</p>
                @else
                    <p class="text-muted">No profile details available.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profile_details" data-toggle="tab">Full Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings_access" data-toggle="tab">Access & Settings</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        {{-- Placeholder for user activity log --}}
                        <p>User activity log will be displayed here (e.g., last login, actions taken).</p>
                        {{-- Example:
                        @foreach($user->activities()->latest()->take(10)->get() as $activity)
                            <div class="post">
                                <p>{{ $activity->description }} <small class="text-muted float-right">{{ $activity->created_at->diffForHumans() }}</small></p>
                            </div>
                        @endforeach
                        --}}
                    </div>

                    <div class="tab-pane" id="profile_details">
                        <h5>Addresses</h5>
                        @forelse($user->addresses as $address)
                            <p><strong>{{ Str::title($address->address_type) }}:</strong> {{ $address->address_line1 }}, {{ $address->address_line2 ? $address->address_line2.',' : '' }} {{ $address->upazila->name_en ?? '' }}, {{ $address->district->name_en ?? '' }}, {{ $address->division->name_en ?? '' }} - {{ $address->postal_code ?? '' }}</p>
                        @empty
                            <p class="text-muted">No addresses found.</p>
                        @endforelse
                        <hr>
                        <h5>Education</h5>
                        @forelse($user->educationRecords as $edu)
                            <p><strong>{{ $edu->degree_title }}</strong> ({{ $edu->degree_level }}) from {{ $edu->institution_name }}, {{ $edu->graduation_year ?? 'N/A' }} (Result: {{ $edu->result_grade ?? 'N/A' }})</p>
                        @empty
                            <p class="text-muted">No education records found.</p>
                        @endforelse
                        <hr>
                        <h5>Professional Experience</h5>
                         @forelse($user->professionalExperiences as $exp)
                            <p><strong>{{ $exp->designation }}</strong> at {{ $exp->organization_name }} ({{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }})</p>
                        @empty
                            <p class="text-muted">No professional experience found.</p>
                        @endforelse
                        <hr>
                        <h5>Social Links</h5>
                         @forelse($user->socialLinks as $link)
                            <p><a href="{{ $link->profile_url }}" target="_blank">{{ Str::title($link->platform_name) }} Profile</a></p>
                        @empty
                            <p class="text-muted">No social links found.</p>
                        @endforelse
                    </div>

                    <div class="tab-pane" id="settings_access">
                        <form action="{{ route('admin.users.updateRoles', $user->id) }}" method="POST" class="form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="inputRoles" class="col-sm-2 col-form-label">Roles</label>
                                <div class="col-sm-10">
                                     <select name="roles[]" id="inputRoles" class="form-control select2-roles" multiple="multiple" data-placeholder="Assign Roles" style="width: 100%;">
                                        @php $userCurrentRoles = $user->roles->pluck('name')->toArray(); @endphp
                                        @foreach(Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                                            <option value="{{ $role->name }}" {{ in_array($role->name, $userCurrentRoles) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Update Roles</button>
                                </div>
                            </div>
                        </form>
                        {{-- Add other settings like suspend user, force password reset etc. --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .profile-user-img { border: 3px solid #adb5bd; margin: 0 auto; padding: 3px; width: 100px; }
        .nav-pills .nav-link.active, .nav-pills .show>.nav-link { color: #fff; background-color: var(--primary); }
        .nav-pills .nav-link { border-radius: var(--radius-sm); color: var(--primary); }
        .nav-pills .nav-link:not(.active):hover { color: var(--primary-hover); }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            min-height: calc(1.5em + 0.625rem * 2 + 2px);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary); border-color: var(--primary-hover); color: white;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255,255,255,0.7);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-roles').select2({
                placeholder: "Assign Roles",
                allowClear: true
            });
        });
    </script>
@endpush
