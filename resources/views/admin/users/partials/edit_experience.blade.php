{{-- Tab 5: Professional Experience --}}
<div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
    <h4 class="mb-0">Professional Experience</h4>
    <button type="button" id="add-experience-item" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Experience</button>
</div>
<div id="experience-repeater-container">
    @php $expIndex = 0; @endphp
    @forelse ($user->professionalExperiences->sortByDesc('start_date') ?? [] as $index => $exp)
        @php $expIndex = $loop->index; @endphp
        <div class="repeater-item experience-repeater-item mb-3">
            <input type="hidden" name="experience[{{ $expIndex }}][id]" value="{{ $exp->id }}">
            <input type="hidden" name="experience[{{ $expIndex }}][_delete]" value="0" class="delete-flag">
            <h6 class="text-muted">Experience Record #{{ $expIndex + 1 }}</h6>
            <hr class="my-2">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="exp_designation_{{ $expIndex }}">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="experience[{{ $expIndex }}][designation]" id="exp_designation_{{ $expIndex }}" class="form-control @error('experience.'.$expIndex.'.designation') is-invalid @enderror" value="{{ old('experience.'.$expIndex.'.designation', $exp->designation) }}">
                    @error('experience.'.$expIndex.'.designation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="exp_organization_name_{{ $expIndex }}">Organization Name <span class="text-danger">*</span></label>
                    <input type="text" name="experience[{{ $expIndex }}][organization_name]" id="exp_organization_name_{{ $expIndex }}" class="form-control @error('experience.'.$expIndex.'.organization_name') is-invalid @enderror" value="{{ old('experience.'.$expIndex.'.organization_name', $exp->organization_name) }}">
                    @error('experience.'.$expIndex.'.organization_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="exp_start_date_{{ $expIndex }}">Start Date <span class="text-danger">*</span></label>
                    <input type="text" name="experience[{{ $expIndex }}][start_date]" id="exp_start_date_{{ $expIndex }}" class="form-control datepicker @error('experience.'.$expIndex.'.start_date') is-invalid @enderror" value="{{ old('experience.'.$expIndex.'.start_date', optional($exp->start_date)->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
                    @error('experience.'.$expIndex.'.start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="exp_end_date_{{ $expIndex }}">End Date</label>
                    <input type="text" name="experience[{{ $expIndex }}][end_date]" id="exp_end_date_{{ $expIndex }}" class="form-control datepicker @error('experience.'.$expIndex.'.end_date') is-invalid @enderror" value="{{ old('experience.'.$expIndex.'.end_date', optional($exp->end_date)->format('Y-m-d')) }}" placeholder="YYYY-MM-DD (or leave blank if current)">
                    @error('experience.'.$expIndex.'.end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="exp_responsibilities_{{ $expIndex }}">Responsibilities</label>
                    <textarea name="experience[{{ $expIndex }}][responsibilities]" id="exp_responsibilities_{{ $expIndex }}" class="form-control @error('experience.'.$expIndex.'.responsibilities') is-invalid @enderror" rows="1">{{ old('experience.'.$expIndex.'.responsibilities', $exp->responsibilities) }}</textarea>
                    @error('experience.'.$expIndex.'.responsibilities') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-2 form-group align-self-center pt-3"> {{-- pt-3 for alignment with label --}}
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="experience[{{ $expIndex }}][is_current_job]" value="1" class="custom-control-input" id="exp_current_job_{{ $expIndex }}" {{ old('experience.'.$expIndex.'.is_current_job', $exp->is_current_job) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="exp_current_job_{{ $expIndex }}">Is Current Job?</label>
                    </div>
                </div>
                <div class="col-md-1 text-right align-self-center">
                    <button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted no-items-text">No professional experience records added yet.</p>
    @endforelse
    <input type="hidden" id="experience-next-index" value="{{ $expIndex + 1 }}">
</div>
@error('experience') <div class="alert alert-danger mt-2">{{ $message }}</div> @enderror
