{{-- Tab 3: Addresses --}}
<div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
    <h4 class="mb-0">Contact Addresses</h4>
    <button type="button" id="add-address-item" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Address</button>
</div>

<div id="address-repeater-container">
    @php $addressIndex = 0; @endphp
    @forelse ($user->addresses->sortBy('address_type') ?? [] as $index => $address)
        @php $addressIndex = $loop->index; @endphp
        <div class="repeater-item address-repeater-item mb-3">
            <input type="hidden" name="addresses[{{ $addressIndex }}][id]" value="{{ $address->id }}">
            <input type="hidden" name="addresses[{{ $addressIndex }}][_delete]" value="0" class="delete-flag">
            <h6 class="text-muted">Address #{{ $addressIndex + 1 }} (Type: {{ Str::title($address->address_type) }})</h6>
            <hr class="my-2">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="address_type_{{ $addressIndex }}">Address Type <span class="text-danger">*</span></label>
                    <select name="addresses[{{ $addressIndex }}][address_type]" id="address_type_{{ $addressIndex }}" class="form-control @error('addresses.'.$addressIndex.'.address_type') is-invalid @enderror">
                        <option value="present" {{ old('addresses.'.$addressIndex.'.address_type', $address->address_type) == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="permanent" {{ old('addresses.'.$addressIndex.'.address_type', $address->address_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="work" {{ old('addresses.'.$addressIndex.'.address_type', $address->address_type) == 'work' ? 'selected' : '' }}>Work</option>
                    </select>
                    @error('addresses.'.$addressIndex.'.address_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-9 form-group">
                    <label for="address_line1_{{ $addressIndex }}">Address Line 1 <span class="text-danger">*</span></label>
                    <input type="text" name="addresses[{{ $addressIndex }}][address_line1]" id="address_line1_{{ $addressIndex }}" class="form-control @error('addresses.'.$addressIndex.'.address_line1') is-invalid @enderror" value="{{ old('addresses.'.$addressIndex.'.address_line1', $address->address_line1) }}">
                    @error('addresses.'.$addressIndex.'.address_line1') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="address_line2_{{ $addressIndex }}">Address Line 2</label>
                    <input type="text" name="addresses[{{ $addressIndex }}][address_line2]" id="address_line2_{{ $addressIndex }}" class="form-control @error('addresses.'.$addressIndex.'.address_line2') is-invalid @enderror" value="{{ old('addresses.'.$index.'.address_line2', $address->address_line2) }}">
                    @error('addresses.'.$addressIndex.'.address_line2') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="division_id_{{ $addressIndex }}">Division</label>
                    <select name="addresses[{{ $addressIndex }}][division_id]" id="division_id_{{ $addressIndex }}" class="form-control division-select @error('addresses.'.$addressIndex.'.division_id') is-invalid @enderror">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division) {{-- $divisions passed from UserController@edit --}}
                            <option value="{{ $division->id }}" {{ old('addresses.'.$addressIndex.'.division_id', $address->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name_en }}</option>
                        @endforeach
                    </select>
                    @error('addresses.'.$addressIndex.'.division_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="district_id_{{ $addressIndex }}">District</label>
                    <select name="addresses[{{ $addressIndex }}][district_id]" id="district_id_{{ $addressIndex }}" class="form-control district-select @error('addresses.'.$addressIndex.'.district_id') is-invalid @enderror" data-selected-district="{{ old('addresses.'.$addressIndex.'.district_id', $address->district_id) }}">
                        <option value="">Select District</option>
                        @if($address->division_id && $address->district_id)
                            @foreach(App\Models\District::where('division_id', $address->division_id)->orderBy('name_en')->get() as $districtOption)
                                <option value="{{ $districtOption->id }}" {{ $address->district_id == $districtOption->id ? 'selected' : '' }}>{{ $districtOption->name_en }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('addresses.'.$addressIndex.'.district_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="upazila_id_{{ $addressIndex }}">Upazila</label>
                    <select name="addresses[{{ $addressIndex }}][upazila_id]" id="upazila_id_{{ $addressIndex }}" class="form-control upazila-select @error('addresses.'.$addressIndex.'.upazila_id') is-invalid @enderror" data-selected-upazila="{{ old('addresses.'.$addressIndex.'.upazila_id', $address->upazila_id) }}">
                        <option value="">Select Upazila</option>
                         @if($address->district_id && $address->upazila_id)
                            @foreach(App\Models\Upazila::where('district_id', $address->district_id)->orderBy('name_en')->get() as $upazilaOption)
                                 <option value="{{ $upazilaOption->id }}" {{ $address->upazila_id == $upazilaOption->id ? 'selected' : '' }}>{{ $upazilaOption->name_en }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('addresses.'.$addressIndex.'.upazila_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label for="postal_code_{{ $addressIndex }}">Postal Code</label>
                    <input type="text" name="addresses[{{ $addressIndex }}][postal_code]" id="postal_code_{{ $addressIndex }}" class="form-control @error('addresses.'.$addressIndex.'.postal_code') is-invalid @enderror" value="{{ old('addresses.'.$addressIndex.'.postal_code', $address->postal_code) }}">
                    @error('addresses.'.$addressIndex.'.postal_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i> Remove</button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted no-items-text">No addresses currently added for this user. Click "Add Address" to begin.</p>
    @endforelse
    <input type="hidden" id="address-next-index" value="{{ $addressIndex + 1 }}">
</div>

@error('addresses') {{-- General error for the addresses array itself --}}
    <div class="alert alert-danger mt-2">{{ $message }}</div>
@enderror
