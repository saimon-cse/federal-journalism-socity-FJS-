@extends('layouts.admin.app')

@section('title', 'Edit User: ' . $user->name)
@section('page-title', 'Edit User Profile')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users List
    </a>
    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i> View Profile
    </a>
@endsection

@section('content')
<form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header p-0 border-bottom-0"> {{-- p-0 to let nav-pills control padding --}}
            {{-- Your custom CSS might style .nav-pills within .card-header differently --}}
            {{-- If tabs are outside card-header, structure it accordingly --}}
            <ul class="nav nav-pills" id="userEditTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-info-tab" data-target="#basic_info_content" type="button" role="tab" aria-controls="basic_info_content" aria-selected="true">Basic Info & Account</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="personal-details-tab" data-target="#personal_details_content" type="button" role="tab" aria-controls="personal_details_content" aria-selected="false">Personal Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="addresses-tab" data-target="#addresses_content" type="button" role="tab" aria-controls="addresses_content" aria-selected="false">Addresses</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="education-tab" data-target="#education_content" type="button" role="tab" aria-controls="education_content" aria-selected="false">Education</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="experience-tab" data-target="#experience_content" type="button" role="tab" aria-controls="experience_content" aria-selected="false">Experience</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-target="#social_content" type="button" role="tab" aria-controls="social_content" aria-selected="false">Social Links</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="userEditTabContent">
                {{-- Tab 1: Basic Info & Account --}}
                <div class="tab-pane fade show active" id="basic_info_content" role="tabpanel" aria-labelledby="basic-info-tab">
                    @include('admin.users.partials.edit_basic_info_account')
                </div>

                {{-- Tab 2: Personal Details (UserProfile) --}}
                <div class="tab-pane fade" id="personal_details_content" role="tabpanel" aria-labelledby="personal-details-tab">
                    @include('admin.users.partials.edit_personal_details')
                </div>

                {{-- Tab 3: Addresses --}}
                <div class="tab-pane fade" id="addresses_content" role="tabpanel" aria-labelledby="addresses-tab">
                    @include('admin.users.partials.edit_addresses')
                </div>

                {{-- Tab 4: Education --}}
                <div class="tab-pane fade" id="education_content" role="tabpanel" aria-labelledby="education-tab">
                    @include('admin.users.partials.edit_education')
                </div>

                {{-- Tab 5: Professional Experience --}}
                <div class="tab-pane fade" id="experience_content" role="tabpanel" aria-labelledby="experience-tab">
                    @include('admin.users.partials.edit_experience')
                </div>

                {{-- Tab 6: Social Links --}}
                <div class="tab-pane fade" id="social_content" role="tabpanel" aria-labelledby="social-tab">
                    @include('admin.users.partials.edit_social_links')
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update User Profile
            </button>
        </div>
    </div>
</form>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Styles from your custom CSS for nav-pills, select2, repeater-item */
        /* Ensure these are specific enough or covered by your main style.css */
        .nav-pills .nav-link { /* From your CSS */
            border-radius: var(--radius-sm);
            color: var(--primary);
            margin-right: 0.25rem; /* If items are buttons, not LIs */
            padding: 0.5rem 1rem; /* Example padding for button-like tabs */
            border: 1px solid transparent;
            background-color: transparent;
        }
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link { /* From your CSS */
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .nav-pills .nav-link:not(.active):hover {
            color: var(--primary-hover);
            background-color: var(--primary-light);
        }
        .select2-container--default .select2-selection--multiple { border: 1px solid var(--border); border-radius: var(--radius); min-height: calc(1.5em + 0.625rem * 2 + 2px); }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: var(--primary); border-color: var(--primary-hover); color: white; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: rgba(255,255,255,0.7); }

        .repeater-item { border: 1px solid var(--border); padding: 1rem; margin-bottom: 1rem; border-radius: var(--radius-md); background-color: #fdfdfd; }
        .repeater-item h6 { font-size: 0.9rem; color: var(--secondary-hover); }
        .repeater-item .btn-danger { /* Adjust alignment if needed */ }
        .card-footer { background-color: var(--bg-light); /* Match your CSS */ }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Wait for the main script.js to handle its DOMContentLoaded
        // or ensure this script block is loaded after your main script.js
        // If this is directly in Blade, it will run after DOMContentLoaded of this page.
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            document.querySelectorAll('.select2-roles').forEach(function(el) {
                $(el).select2({ // jQuery still often used for plugins
                    placeholder: el.dataset.placeholder || "Select options",
                    allowClear: true // $(el).data('allow-clear') // for data-allow-clear="true"
                });
            });
            // Initialize Datepickers
            document.querySelectorAll('.datepicker').forEach(function(el) {
                 $(el).datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: "bottom auto" // Adjust as needed
                });
            });

            // Handle email verification checkbox for clearing (specific to user edit form)
            const emailVerifiedCheckbox = document.getElementById('email_verified');
            const emailVerifiedClearedInput = document.getElementById('email_verified_at_cleared_input');
            if (emailVerifiedCheckbox && emailVerifiedClearedInput) {
                emailVerifiedCheckbox.addEventListener('change', function() {
                    if (!this.checked && {{ optional($user->email_verified_at)->timestamp ? 'true' : 'false' }}) {
                        emailVerifiedClearedInput.value = '1';
                    } else {
                        emailVerifiedClearedInput.value = '0';
                    }
                });
            }

            // --- Repeater Logic ---
            function initializeRepeater(containerSelector, itemSelector, addButtonSelector, itemHtmlCallback, nextIndexInputId) {
                const container = document.querySelector(containerSelector);
                const addButton = document.querySelector(addButtonSelector);
                const nextIndexInput = document.getElementById(nextIndexInputId);

                if (!container || !addButton || !nextIndexInput) {
                    // console.warn("Repeater elements not found for:", containerSelector);
                    return;
                }

                let nextIndex = parseInt(nextIndexInput.value || container.querySelectorAll(itemSelector).length);

                addButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    let currentIndex = 0;
                    container.querySelectorAll(itemSelector).forEach(item => {
                        const firstInput = item.querySelector('input, select, textarea');
                        if (firstInput && firstInput.name) {
                            const match = firstInput.name.match(/\[(\d+)\]/);
                            if (match && parseInt(match[1]) >= currentIndex) {
                                currentIndex = parseInt(match[1]) + 1;
                            }
                        }
                    });
                    nextIndex = Math.max(currentIndex, container.querySelectorAll(itemSelector).length);

                    const newItemHtml = itemHtmlCallback(nextIndex);
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newItemHtml.trim();
                    const newItemElement = tempDiv.firstChild;

                    const noItemsText = container.querySelector('.no-items-text');
                    if (noItemsText) noItemsText.style.display = 'none';

                    container.appendChild(newItemElement);
                    nextIndexInput.value = nextIndex + 1;

                    // Initialize datepickers on new item
                    newItemElement.querySelectorAll('.datepicker').forEach(dp => {
                        $(dp).datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, orientation: "bottom auto" });
                    });
                });

                container.addEventListener('click', function(e) {
                    if (e.target.closest && e.target.closest('.remove-repeater-item')) {
                        e.preventDefault();
                        const itemToRemove = e.target.closest(itemSelector);
                        if (itemToRemove) {
                            const idInput = itemToRemove.querySelector('input[name$="[id]"]');
                            const deleteFlagInput = itemToRemove.querySelector('.delete-flag'); // Ensure class="delete-flag" on _delete input

                            if (idInput && idInput.value && deleteFlagInput) {
                                deleteFlagInput.value = '1';
                                itemToRemove.style.display = 'none';
                            } else {
                                itemToRemove.remove();
                            }

                            if (container.querySelectorAll(itemSelector + ':not([style*="display: none"])').length === 0) {
                                const noItemsText = container.querySelector('.no-items-text');
                                if (noItemsText) noItemsText.style.display = 'block';
                            }
                        }
                    }
                });
            }

            // Address Repeater
            const divisionOptionsHtml = `@foreach($divisions as $division) <option value="{{ $division->id }}">{{ $division->name_en }}</option> @endforeach`;
            initializeRepeater('#address-repeater-container', '.address-repeater-item', '#add-address-item', function(index) {
                return `
                    <div class="repeater-item address-repeater-item mb-3">
                        <input type="hidden" name="addresses[${index}][id]" value="">
                        <input type="hidden" name="addresses[${index}][_delete]" value="0" class="delete-flag">
                        <h6 class="text-muted">New Address #${index + 1}</h6><hr class="my-2">
                        <div class="row">
                            <div class="col-md-3 form-group"> <label>Address Type*</label> <select name="addresses[${index}][address_type]" class="form-control"><option value="present">Present</option><option value="permanent">Permanent</option><option value="work">Work</option></select> </div>
                            <div class="col-md-9 form-group"> <label>Address Line 1*</label> <input type="text" name="addresses[${index}][address_line1]" class="form-control"> </div>
                            <div class="col-md-6 form-group"> <label>Address Line 2</label> <input type="text" name="addresses[${index}][address_line2]" class="form-control"> </div>
                            <div class="col-md-3 form-group"> <label>Division</label> <select name="addresses[${index}][division_id]" class="form-control division-select"><option value="">Select Division</option>${divisionOptionsHtml}</select> </div>
                            <div class="col-md-3 form-group"> <label>District</label> <select name="addresses[${index}][district_id]" class="form-control district-select"><option value="">Select District</option></select> </div>
                            <div class="col-md-3 form-group"> <label>Upazila</label> <select name="addresses[${index}][upazila_id]" class="form-control upazila-select"><option value="">Select Upazila</option></select> </div>
                            <div class="col-md-3 form-group"> <label>Postal Code</label> <input type="text" name="addresses[${index}][postal_code]" class="form-control"> </div>
                            <div class="col-md-12 text-right"> <button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i> Remove</button> </div>
                        </div>
                    </div>`;
            }, 'address-next-index');

            initializeRepeater('#education-repeater-container', '.education-repeater-item', '#add-education-item', function(index) {
                return `
                    <div class="repeater-item education-repeater-item mb-3">
                        <input type="hidden" name="education[${index}][id]" value="">
                        <input type="hidden" name="education[${index}][_delete]" value="0" class="delete-flag">
                        <h6 class="text-muted">New Education #${index + 1}</h6><hr class="my-2">
                        <div class="row">
                            <div class="col-md-3 form-group"><label>Degree Level*</label><input type="text" name="education[${index}][degree_level]" class="form-control"></div>
                            <div class="col-md-5 form-group"><label>Degree Title*</label><input type="text" name="education[${index}][degree_title]" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>Major Subject</label><input type="text" name="education[${index}][major_subject]" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Institution*</label><input type="text" name="education[${index}][institution_name]" class="form-control"></div>
                            <div class="col-md-3 form-group"><label>Grad. Year</label><input type="number" name="education[${index}][graduation_year]" class="form-control" min="1950" max="${new Date().getFullYear() + 5}"></div>
                            <div class="col-md-3 form-group"><label>Result/Grade</label><input type="text" name="education[${index}][result_grade]" class="form-control"></div>
                            <div class="col-md-12 text-right"><button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i> Remove</button></div>
                        </div>
                    </div>`;
            }, 'education-next-index');

            initializeRepeater('#experience-repeater-container', '.experience-repeater-item', '#add-experience-item', function(index) {
                return `
                    <div class="repeater-item experience-repeater-item mb-3">
                        <input type="hidden" name="experience[${index}][id]" value="">
                        <input type="hidden" name="experience[${index}][_delete]" value="0" class="delete-flag">
                         <h6 class="text-muted">New Experience #${index + 1}</h6><hr class="my-2">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Designation*</label><input type="text" name="experience[${index}][designation]" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Organization*</label><input type="text" name="experience[${index}][organization_name]" class="form-control"></div>
                            <div class="col-md-3 form-group"><label>Start Date*</label><input type="text" name="experience[${index}][start_date]" class="form-control datepicker"></div>
                            <div class="col-md-3 form-group"><label>End Date</label><input type="text" name="experience[${index}][end_date]" class="form-control datepicker"></div>
                            <div class="col-md-3 form-group"><label>Responsibilities</label><textarea name="experience[${index}][responsibilities]" class="form-control" rows="1"></textarea></div>
                            <div class="col-md-2 form-group align-self-center pt-3"><div class="custom-control custom-checkbox"><input type="checkbox" name="experience[${index}][is_current_job]" value="1" class="custom-control-input" id="exp_current_job_new_${index}"><label class="custom-control-label" for="exp_current_job_new_${index}">Current Job</label></div></div>
                            <div class="col-md-1 text-right align-self-center"><button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>`;
            }, 'experience-next-index');

            initializeRepeater('#social-links-repeater-container', '.social-links-repeater-item', '#add-social-link-item', function(index) {
                return `
                    <div class="repeater-item social-links-repeater-item mb-3">
                        <input type="hidden" name="social_links[${index}][id]" value="">
                        <input type="hidden" name="social_links[${index}][_delete]" value="0" class="delete-flag">
                        <h6 class="text-muted">New Social Link #${index + 1}</h6><hr class="my-2">
                        <div class="row">
                            <div class="col-md-5 form-group"><label>Platform Name*</label><input type="text" name="social_links[${index}][platform_name]" class="form-control" placeholder="e.g., Facebook, LinkedIn"></div>
                            <div class="col-md-6 form-group"><label>Profile URL*</label><input type="url" name="social_links[${index}][profile_url]" class="form-control" placeholder="https://..."></div>
                            <div class="col-md-1 text-right align-self-center"><button type="button" class="btn btn-sm btn-danger remove-repeater-item"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>`;
            }, 'social-links-next-index');

            // --- AJAX for dependent dropdowns (Districts, Upazilas) ---
            // Using event delegation for dynamically added items
            document.getElementById('userEditTabContent').addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('division-select')) {
                    const divisionId = e.target.value;
                    const repeaterItem = e.target.closest('.address-repeater-item');
                    if (!repeaterItem) return;

                    const districtSelect = repeaterItem.querySelector('.district-select');
                    const upazilaSelect = repeaterItem.querySelector('.upazila-select');

                    districtSelect.innerHTML = '<option value="">Loading...</option>';
                    if(upazilaSelect) upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

                    if (divisionId) {
                        const districtUrl = '{{ route("api.geography.districts.by_division", ["division" => ":divisionId"]) }}'.replace(':divisionId', divisionId);
                        fetch(districtUrl)
                            .then(response => response.json())
                            .then(data => {
                                let options = '<option value="">Select District</option>';
                                data.forEach(district => {
                                    options += `<option value="${district.id}">${district.name_en}</option>`;
                                });
                                districtSelect.innerHTML = options;
                                const preSelectedDistrict = districtSelect.dataset.selectedDistrict;
                                if (preSelectedDistrict) {
                                    districtSelect.value = preSelectedDistrict;
                                    // districtSelect.dispatchEvent(new Event('change')); // Trigger change to load upazilas if needed
                                    // Manually trigger for vanilla JS if the select was just populated
                                    if (districtSelect.value) { // if a value got selected
                                        const changeEvent = new Event('change', { bubbles: true });
                                        districtSelect.dispatchEvent(changeEvent);
                                    }
                                    delete districtSelect.dataset.selectedDistrict; // Clear after use
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching districts:", error);
                                districtSelect.innerHTML = '<option value="">Error loading</option>';
                            });
                    } else {
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                    }
                } else if (e.target && e.target.classList.contains('district-select')) {
                    const districtId = e.target.value;
                    const repeaterItem = e.target.closest('.address-repeater-item');
                     if (!repeaterItem) return;
                    const upazilaSelect = repeaterItem.querySelector('.upazila-select');

                    if(upazilaSelect) upazilaSelect.innerHTML = '<option value="">Loading...</option>';

                    if (districtId) {
                        const upazilaUrl = '{{ route("api.geography.upazilas.by_district", ["district" => ":districtId"]) }}'.replace(':districtId', districtId);
                        fetch(upazilaUrl)
                            .then(response => response.json())
                            .then(data => {
                                let options = '<option value="">Select Upazila</option>';
                                data.forEach(upazila => {
                                    options += `<option value="${upazila.id}">${upazila.name_en}</option>`;
                                });
                                if(upazilaSelect) {
                                    upazilaSelect.innerHTML = options;
                                    const preSelectedUpazila = upazilaSelect.dataset.selectedUpazila;
                                    if (preSelectedUpazila) {
                                        upazilaSelect.value = preSelectedUpazila;
                                        delete upazilaSelect.dataset.selectedUpazila;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching upazilas:", error);
                                if(upazilaSelect) upazilaSelect.innerHTML = '<option value="">Error loading</option>';
                            });
                    } else {
                       if(upazilaSelect) upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                    }
                }
            });

            // Trigger change on existing division selects on page load
            document.querySelectorAll('.division-select').forEach(function(selectElement){
                if(selectElement.value){
                    // Create and dispatch the event
                    const event = new Event('change', { 'bubbles': true });
                    selectElement.dispatchEvent(event);
                }
            });

            // Tab Activation using Bootstrap's JS (if available) or custom logic
            // Your script.js seems to have custom tab logic, ensure it targets these new tab IDs/data-targets
            // Or rely on Bootstrap's data-toggle="tab" if its JS is loaded and not conflicting.
            // The HTML is now set up for Bootstrap 5 style tabs (using data-bs-target and button roles)
            // If using Bootstrap 4, it would be <a class="nav-link" data-toggle="tab" href="#target_id">
            // My provided HTML for tabs in this blade uses:
            // <button class="nav-link active" id="basic-info-tab" data-target="#basic_info_content" type="button" role="tab" ...
            // This will rely on your custom tab JS that uses data-target.
            // If Bootstrap's tab JS is active and you want to use it, you'd change data-target to href or data-bs-target
            // and add data-bs-toggle="tab" (for BS5) or data-toggle="tab" (for BS4).
            // The current setup is for your custom JS.

        }); // End of DOMContentLoaded for edit page scripts
    </script>
@endsection
