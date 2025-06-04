@csrf
<div class="row">
    <div class="col-md-6 form-group">
        <label for="account_name">Account Name <span class="text-danger">*</span></label>
        <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $paymentAccount->account_name ?? '') }}" required>
        @error('account_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="account_type">Account Type <span class="text-danger">*</span></label>
        <select name="account_type" id="account_type" class="form-control @error('account_type') is-invalid @enderror" required>
            <option value="">Select Type</option>
            @foreach($accountTypes as $value => $label)
                <option value="{{ $value }}" {{ old('account_type', $paymentAccount->account_type ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('account_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="account_identifier">Account Identifier (Number/ID) <span class="text-danger">*</span></label>
        <input type="text" name="account_identifier" id="account_identifier" class="form-control @error('account_identifier') is-invalid @enderror" value="{{ old('account_identifier', $paymentAccount->account_identifier ?? '') }}" required>
        @error('account_identifier') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="account_holder_name">Account Holder Name</label>
        <input type="text" name="account_holder_name" id="account_holder_name" class="form-control @error('account_holder_name') is-invalid @enderror" value="{{ old('account_holder_name', $paymentAccount->account_holder_name ?? '') }}">
        @error('account_holder_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>
<div class="bank-details" style="{{ old('account_type', $paymentAccount->account_type ?? '') == 'bank_account' ? '' : 'display:none;' }}">
    <h5 class="mt-3">Bank Specific Details</h5>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="bank_name">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $paymentAccount->bank_name ?? '') }}">
            @error('bank_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 form-group">
            <label for="branch_name">Branch Name</label>
            <input type="text" name="branch_name" id="branch_name" class="form-control @error('branch_name') is-invalid @enderror" value="{{ old('branch_name', $paymentAccount->branch_name ?? '') }}">
            @error('branch_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 form-group">
            <label for="routing_number">Routing Number</label>
            <input type="text" name="routing_number" id="routing_number" class="form-control @error('routing_number') is-invalid @enderror" value="{{ old('routing_number', $paymentAccount->routing_number ?? '') }}">
            @error('routing_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
<div class="form-group">
    <label for="instructions_for_payer">Instructions for Payer</label>
    <textarea name="instructions_for_payer" id="instructions_for_payer" class="form-control @error('instructions_for_payer') is-invalid @enderror" rows="3">{{ old('instructions_for_payer', $paymentAccount->instructions_for_payer ?? '') }}</textarea>
    @error('instructions_for_payer') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $paymentAccount->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Active (can be selected by users for payment)</label>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accountTypeSelect = document.getElementById('account_type');
        const bankDetailsDiv = document.querySelector('.bank-details');

        function toggleBankDetails() {
            if (accountTypeSelect.value === 'bank_account') {
                bankDetailsDiv.style.display = 'block';
            } else {
                bankDetailsDiv.style.display = 'none';
            }
        }
        if(accountTypeSelect) {
            accountTypeSelect.addEventListener('change', toggleBankDetails);
            toggleBankDetails(); // Initial check
        }
    });
</script>
@endpush
