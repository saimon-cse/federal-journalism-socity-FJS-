@extends('layouts.admin.app')

@section('title', 'Create New Role')
@section('page-title', 'Add New Role')

@section('header-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Role Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Assign Permissions</label>
                @error('permissions') <span class="d-block invalid-feedback mb-2">{{ $message }}</span> @enderror

                <div class="row">
                    @forelse ($permissions as $group => $groupedPermissions)
                        <div class="col-md-4 mb-3">
                            <div class="card card-outline card-secondary h-100">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">{{ Str::title(str_replace('-', ' ', $group)) }}</h3>
                                    <div class="card-tools">
                                         <input type="checkbox" class="select-all-permissions" data-group="{{ $group }}" title="Select/Deselect All in {{ Str::title($group) }}">
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($groupedPermissions as $permission)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input permission-checkbox"
                                               id="permission_{{ $permission->id }}"
                                               name="permissions[]" value="{{ $permission->name }}"
                                               data-group="{{ $group }}"
                                               {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                            {{ Str::title(str_replace('-', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">No permissions defined yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select-all-permissions').on('change', function() {
            var group = $(this).data('group');
            var isChecked = $(this).is(':checked');
            $('.permission-checkbox[data-group="' + group + '"]').prop('checked', isChecked);
        });

        $('.permission-checkbox').on('change', function() {
            var group = $(this).data('group');
            var allInGroup = $('.permission-checkbox[data-group="' + group + '"]');
            var allChecked = allInGroup.length === allInGroup.filter(':checked').length;
            $('.select-all-permissions[data-group="' + group + '"]').prop('checked', allChecked);
        });

        // Initial check for select-all checkboxes
        $('.select-all-permissions').each(function() {
            var group = $(this).data('group');
            var allInGroup = $('.permission-checkbox[data-group="' + group + '"]');
            if (allInGgroup.length > 0) { // only if there are checkboxes in the group
                 var allChecked = allInGroup.length === allInGroup.filter(':checked').length;
                $(this).prop('checked', allChecked);
            }
        });
    });
</script>
@endpush
