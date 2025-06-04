{{-- Tab 6: Social Links --}}
<div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
    <h4 class="mb-0">Social Media Links</h4>
    <button type="button" id="add-social-link-item" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Social Link</button>
</div>
<div id="social-links-repeater-container">
    @php $linkIndex = 0; @endphp
    @forelse ($user->socialLinks ?? [] as $index => $link)
        @php $linkIndex = $loop->index; @endphp
        <div class="repeater-item social-links-repeater-item mb-3">
            <input type="hidden" name="social_links[{{ $linkIndex }}][id]" value="{{ $link->id }}">
            <input type="hidden" name="social_links[{{ $linkIndex }}][_delete]" value="0" class="delete-flag">
            <h6 class="text-muted">Social Link #{{ $linkIndex + 1 }}</h6>
            <hr class="my-2">
            <div class="row">
                <div class="col-md-5 form-group">
                    <label for="social_platform_name_{{ $linkIndex }}">Platform Name <span class="text-danger">*</span></label>
                    <input type="text" name="social_links[{{ $linkIndex }}][platform_name]" id="social_platform_name_{{ $linkIndex }}" class="form-control @error('social_links.'.$linkIndex.'.platform_name') is-invalid @enderror" value="{{ old('social_links.'.$linkIndex.'.platform_name', $link->platform_name) }}" placeholder="e.g., Facebook, LinkedIn, Twitter">
                    @error('social_links.'.$linkIndex.'.platform_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="social_profile_url_{{ $linkIndex }}">Profile URL <span class="text-danger">*</span></label>
                    <input type="url" name="social_links[{{ $linkIndex }}][profile_url]" id="social_profile_url_{{ $linkIndex }}" class="form-control @error('social_links.'.$linkIndex.'.profile_url') is-invalid @enderror" value="{{ old('social_links.'.$linkIndex.'.profile_url', $link->profile_url) }}" placeholder="https://www.example.com/username">
                    @error('social_links.'.$linkIndex.'.profile_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-1 text-right align-self-center">
                    <button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted no-items-text">No social links currently added for this user.</p>
    @endforelse
    <input type="hidden" id="social-links-next-index" value="{{ $linkIndex + 1 }}">
</div>
@error('social_links') <div class="alert alert-danger mt-2">{{ $message }}</div> @enderror
