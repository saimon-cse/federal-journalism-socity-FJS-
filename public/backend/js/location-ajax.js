/**
 * Location data handling script
 * Handles AJAX requests for loading divisions, districts, and upazilas
 */

// URLs for AJAX requests
const DISTRICTS_URL = '/districts/by-division';
const UPAZILAS_URL = '/upazilas/by-district';

/**
 * Load all divisions
 * @param {Function} callback - Optional callback after loading divisions
 */
function loadDivisions(callback) {
    // Make AJAX request to get all divisions
    fetch('/divisions/get')
        .then(response => response.json())
        .then(data => {
            // Get select elements
            const permanentDivision = document.getElementById('permanent_division');
            const presentDivision = document.getElementById('present_division');

            if (!permanentDivision || !presentDivision) {
                console.error('Division select elements not found');
                return;
            }

            // Clear existing options
            permanentDivision.innerHTML = '<option value="">Select Division</option>';
            presentDivision.innerHTML = '<option value="">Select Division</option>';

            // Add new options
            data.forEach(division => {
                const option = `<option value="${division.id}">${division.name}</option>`;
                permanentDivision.innerHTML += option;
                presentDivision.innerHTML += option;
            });

            // Execute callback if provided
            if (typeof callback === 'function') {
                callback(data);
            }
        })
        .catch(error => console.error('Error loading divisions:', error));
}

/**
 * Load districts based on division selection
 * @param {string} addressType - 'permanent' or 'present'
 * @returns {Promise} - Promise object representing the request
 */
function getDistricts(addressType) {
    const divisionSelect = document.getElementById(`${addressType}_division`);
    const districtSelect = document.getElementById(`${addressType}_district`);
    const upazilaSelect = document.getElementById(`${addressType}_upazila`);

    if (!divisionSelect || !districtSelect || !upazilaSelect) {
        console.error('Select elements not found');
        return Promise.reject('Select elements not found');
    }

    // Clear existing options
    districtSelect.innerHTML = '<option value="">Select District</option>';
    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

    if (!divisionSelect.value) {
        return Promise.resolve([]);
    }

    // Make AJAX request with the division ID
    return fetch(`${DISTRICTS_URL}?division_id=${divisionSelect.value}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Add new options
            data.forEach(district => {
                districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
            });
            return data;
        })
        .catch(error => {
            console.error('Error loading districts:', error);
            return [];
        });
}

/**
 * Load upazilas based on district selection
 * @param {string} addressType - 'permanent' or 'present'
 * @returns {Promise} - Promise object representing the request
 */
function getUpazilas(addressType) {
    const districtSelect = document.getElementById(`${addressType}_district`);
    const upazilaSelect = document.getElementById(`${addressType}_upazila`);

    if (!districtSelect || !upazilaSelect) {
        console.error('Select elements not found');
        return Promise.reject('Select elements not found');
    }

    // Clear existing options
    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

    if (!districtSelect.value) {
        return Promise.resolve([]);
    }

    // Make AJAX request with the district ID
    return fetch(`${UPAZILAS_URL}?district_id=${districtSelect.value}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Add new options
            data.forEach(upazila => {
                upazilaSelect.innerHTML += `<option value="${upazila.id}">${upazila.name}</option>`;
            });
            return data;
        })
        .catch(error => {
            console.error('Error loading upazilas:', error);
            return [];
        });
}

/**
 * Setup "Same as Permanent Address" checkbox functionality
 */
function setupSameAsAddressCheckbox() {
    const sameAsPermanentCheckbox = document.getElementById('same_as_permanent');
    const presentAddressSection = document.getElementById('present_address_section');

    if (!sameAsPermanentCheckbox || !presentAddressSection) {
        return;
    }

    sameAsPermanentCheckbox.addEventListener('change', function() {
        if (this.checked) {
            presentAddressSection.style.display = 'none';

            // Copy permanent address values to present address fields
            document.getElementById('present_division').value = document.getElementById('permanent_division').value;
            document.getElementById('present_district').value = document.getElementById('permanent_district').value;
            document.getElementById('present_upazila').value = document.getElementById('permanent_upazila').value;
            document.getElementById('present_address').value = document.getElementById('permanent_address').value;
        } else {
            presentAddressSection.style.display = 'block';
        }
    });
}

/**
 * Initialize all location select fields and event listeners
 */
function initLocationSelects() {
    // Load divisions on page load
    loadDivisions();

    // Set event listeners for division and district select elements
    const permanentDivision = document.getElementById('permanent_division');
    const presentDivision = document.getElementById('present_division');
    const permanentDistrict = document.getElementById('permanent_district');
    const presentDistrict = document.getElementById('present_district');

    if (permanentDivision) {
        permanentDivision.addEventListener('change', () => getDistricts('permanent'));
    }

    if (presentDivision) {
        presentDivision.addEventListener('change', () => getDistricts('present'));
    }

    if (permanentDistrict) {
        permanentDistrict.addEventListener('change', () => getUpazilas('permanent'));
    }

    if (presentDistrict) {
        presentDistrict.addEventListener('change', () => getUpazilas('present'));
    }

    // Setup checkbox for copying permanent address to present address
    setupSameAsAddressCheckbox();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initLocationSelects);
