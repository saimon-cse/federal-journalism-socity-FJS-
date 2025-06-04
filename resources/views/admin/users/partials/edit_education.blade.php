{{-- Tab 4: Education Records --}}
<div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
    <h4 class="mb-0">Education Records</h4>
    <button type="button" id="add-education-item" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Education</button>
</div>

<div id="education-repeater-container">
    @php $eduIndex = 0; @endphp
    @forelse($user->educationRecords ?? [] as $index => $edu)
        @php $eduIndex = $loop->index; @endphp
        <div class="repeater-item education-repeater-item mb-3">
            <input type="hidden" name="education[{{ $eduIndex }}][id]" value="{{ $edu->id }}">
            <input type="hidden" name="education[{{ $eduIndex }}][_delete]" value="0" class="delete-flag">
            <h6 class="text-muted">Education Record #{{ $eduIndex + 1 }}</h6>
            <hr class="my-2">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="edu_degree_level_{{ $eduIndex }}">Degree Level <span class="text-danger">*</span></label>
                    <input type="text" name="education[{{ $eduIndex }}][degree_level]" id="edu_degree_level_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.degree_level') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.degree_level', $edu->degree_level) }}">
                    @error('education.'.$eduIndex.'.degree_level') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-5 form-group">
                    <label for="edu_degree_title_{{ $eduIndex }}">Degree Title <span class="text-danger">*</span></label>
                    <input type="text" name="education[{{ $eduIndex }}][degree_title]" id="edu_degree_title_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.degree_title') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.degree_title', $edu->degree_title) }}">
                    @error('education.'.$eduIndex.'.degree_title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label for="edu_major_subject_{{ $eduIndex }}">Major Subject</label>
                    <input type="text" name="education[{{ $eduIndex }}][major_subject]" id="edu_major_subject_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.major_subject') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.major_subject', $edu->major_subject) }}">
                    @error('education.'.$eduIndex.'.major_subject') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="edu_institution_name_{{ $eduIndex }}">Institution Name <span class="text-danger">*</span></label>
                    <input type="text" name="education[{{ $eduIndex }}][institution_name]" id="edu_institution_name_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.institution_name') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.institution_name', $edu->institution_name) }}">
                    @error('education.'.$eduIndex.'.institution_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="edu_graduation_year_{{ $eduIndex }}">Graduation Year</label>
                    <input type="number" name="education[{{ $eduIndex }}][graduation_year]" id="edu_graduation_year_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.graduation_year') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.graduation_year', $edu->graduation_year) }}" min="1950" max="{{ date('Y') + 5 }}">
                    @error('education.'.$eduIndex.'.graduation_year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="edu_result_grade_{{ $eduIndex }}">Result/Grade</label>
                    <input type="text" name="education[{{ $eduIndex }}][result_grade]" id="edu_result_grade_{{ $eduIndex }}" class="form-control @error('education.'.$eduIndex.'.result_grade') is-invalid @enderror" value="{{ old('education.'.$eduIndex.'.result_grade', $edu->result_grade) }}">
                    @error('education.'.$eduIndex.'.result_grade') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i> Remove</button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted no-items-text">No education records currently added for this user.</p>
    @endforelse
    <input type="hidden" id="education-next-index" value="{{ $eduIndex + 1 }}">
</div>
@error('education') <div class="alert alert-danger mt-2">{{ $message }}</div> @enderror
