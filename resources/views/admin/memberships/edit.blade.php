@extends('layouts.admin.app')

@section('title', 'Edit Membership Record')
@section('page-title')
    Edit Membership: <span class="text-primary">{{ $membership->membershipType->name ?? 'N/A' }}</span> for {{ $membership->user->name ?? 'N/A' }}
@endsection

@section('header-actions')
    <a href="{{ route('admin.memberships.show', $membership->id) }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to Details
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Membership Details (Admin Override)</h3>
        </div>
        <form action="{{ route('admin.memberships.update', $membership->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Caution:</strong> Manually changing these details can affect membership status and payment history. Proceed with care.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_info">Member</label>
                            <input type="text" class="form-control" id="user_info" value="{{ $membership->user->name ?? 'N/A' }} ({{ $membership->user->email ?? 'N/A' }})" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="membership_type_id">Membership Type <span class="text-danger">*</span></label>
                            <select name="membership_type_id" id="membership_type_id" class="form-select @error('membership_type_id') is-invalid @enderror" required>
                                @foreach($allMembershipTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('membership_type_id', $membership->membership_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('membership_type_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach($statuses as $statusKey)
                                    <option value="{{ $statusKey }}" {{ old('status', $membership->status) == $statusKey ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $statusKey)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $membership->start_date ? $membership->start_date->format('Y-m-d') : '') }}">
                            @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $membership->end_date ? $membership->end_date->format('Y-m-d') : '') }}">
                            @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- START: Conditional fields for manual activation payment recording --}}
                <div id="manual_activation_payment_fields" style="display: {{ old('status', $membership->status) == 'active' && !in_array($membership->getOriginal('status'), ['active']) ? 'block' : 'none' }};">
                    <hr>
                    <h5 class="mb-3 text-info">Record Payment for Manual Activation</h5>
                    <p class="text-muted"><small>If you are activating this membership manually (e.g., payment received offline), please provide the payment details below. This will create a corresponding payment and ledger entry.</small></p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="manual_activation_payment_account_id">Payment Received In Account <span class="text-danger">*</span></label>
                                <select name="manual_activation_payment_account_id" id="manual_activation_payment_account_id" class="form-select @error('manual_activation_payment_account_id') is-invalid @enderror">
                                    <option value="">-- Select Account --</option>
                                    @foreach(App\Models\PaymentAccount::where('is_active', true)->orderBy('account_name')->get() as $account)
                                        <option value="{{ $account->id }}" {{ old('manual_activation_payment_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_name }} ({{ $account->account_provider }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('manual_activation_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="manual_activation_payment_amount">Payment Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('manual_activation_payment_amount') is-invalid @enderror" id="manual_activation_payment_amount" name="manual_activation_payment_amount" value="{{ old('manual_activation_payment_amount', $membership->membershipType->getFeeForCycle('annual') ?? '0.00') }}">
                                @error('manual_activation_payment_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="manual_activation_transaction_ref">Transaction Reference (Optional)</label>
                        <input type="text" class="form-control @error('manual_activation_transaction_ref') is-invalid @enderror" id="manual_activation_transaction_ref" name="manual_activation_transaction_ref" value="{{ old('manual_activation_transaction_ref') }}">
                        @error('manual_activation_transaction_ref') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                {{-- END: Conditional fields --}}


                <div class="form-group mb-3">
                    <label for="remarks">Admin Remarks</label>
                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3">{{ old('remarks', $membership->remarks) }}</textarea>
                    @error('remarks') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Membership</button>
                <a href="{{ route('admin.memberships.show', $membership->id) }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const manualActivationFields = document.getElementById('manual_activation_payment_fields');
    const paymentAccountSelect = document.getElementById('manual_activation_payment_account_id');
    const paymentAmountInput = document.getElementById('manual_activation_payment_amount');
    const paymentRefInput = document.getElementById('manual_activation_transaction_ref');

    // Store original status to compare on change
    const originalStatus = "{{ $membership->getOriginal('status') }}";

    function toggleManualPaymentFields() {
        if (statusSelect.value === 'active' && originalStatus !== 'active') {
            manualActivationFields.style.display = 'block';
            // Make fields required if activating from a non-active state
            if (paymentAccountSelect) paymentAccountSelect.setAttribute('required', 'required');
            if (paymentAmountInput) paymentAmountInput.setAttribute('required', 'required');
            // paymentRefInput can remain optional
        } else {
            manualActivationFields.style.display = 'none';
             if (paymentAccountSelect) paymentAccountSelect.removeAttribute('required');
             if (paymentAmountInput) paymentAmountInput.removeAttribute('required');
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleManualPaymentFields);
        toggleManualPaymentFields(); // Initial check on page load
    }
});
</script>
@endsection
