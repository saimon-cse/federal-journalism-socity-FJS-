@extends('layouts.admin.app') {{-- Or your main frontend layout --}}

@section('title', 'Apply for Membership')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Membership Application</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <p class="mb-4">Please select your desired membership type to proceed.</p>

                    <form action="{{ route('user.membership.apply.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="membership_type" class="form-label">Select Membership Type <span class="text-danger">*</span></label>
<select name="membership_type_id" id="membership_type_id" class="form-select @error('membership_type_id') is-invalid @enderror" required>
    <option value="">-- Select a Type --</option>
    @foreach($membershipTypes as $type)
        <option value="{{ $type->id }}" {{ old('membership_type_id') == $type->id ? 'selected' : '' }}
                data-fee-annual="{{ $type->annual_amount }}"
                data-fee-monthly="{{ $type->monthly_amount }}">
            {{ $type->name }}
            @if($type->annual_amount)
                - BDT {{ number_format($type->annual_amount, 2) }} (Annual)
            @elseif($type->monthly_amount)
                - BDT {{ number_format($type->monthly_amount, 2) }} (Monthly)
            @endif
            @if($type->membership_duration)
                ({{ $type->membership_duration }})
            @endif
        </option>
    @endforeach
</select>
{{-- Optional: Add a radio/select for user to choose monthly/annual if both exist for a type --}}
                            @error('membership_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Add NID/Photo upload fields here if they are NOT part of the main user profile
                             and are specifically required for membership application.
                        <div class="form-group mb-3">
                            <label for="nid_document" class="form-label">NID Copy (PDF, JPG, PNG - Max 2MB)</label>
                            <input type="file" name="nid_document" id="nid_document" class="form-control @error('nid_document') is-invalid @enderror">
                            @error('nid_document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        --}}

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane"></i> Apply Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
