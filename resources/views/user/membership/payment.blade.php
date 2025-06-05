@extends('layouts.admin.app')

@section('title', 'Membership Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Complete Your Membership Payment</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <p>You are applying for: <strong>{{ ucfirst(str_replace('_', ' ', $membership->membership_type)) }} Membership</strong>.</p>
                    <p>Amount Due: <strong class="text-danger">BDT {{ number_format($fee, 2) }}</strong></p>
                    <hr>
                    <h5 class="mb-3">Payment Instructions:</h5>

                    @if($paymentMethods->isEmpty())
                        <div class="alert alert-warning">
                            No manual payment methods are currently configured. Please contact support.
                        </div>
                    @else
                        <p>Please make your payment to one of the following accounts and then submit your payment details below.</p>

                        <form action="{{ route('user.membership.payment.process', $membership->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="payment_method_id" class="form-label">Payment Made To <span class="text-danger">*</span></label>
                                <select name="payment_method_id" id="payment_method_id" class="form-select @error('payment_method_id') is-invalid @enderror" required>
                                    <option value="">-- Select Account You Paid To --</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" data-instructions="{{ $method->defaultManualAccount->manual_payment_instructions }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                            {{ $method->name }} ({{ $method->defaultManualAccount->account_name }} - {{ $method->defaultManualAccount->account_identifier }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div id="payment_instructions_display" class="alert alert-info" style="display:none;">
                                <!-- JS will populate this -->
                            </div>


                            <div class="form-group mb-3">
                                <label for="transaction_id" class="form-label">Transaction ID / Reference No. <span class="text-danger">*</span></label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" value="{{ old('transaction_id') }}" required>
                                @error('transaction_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="payment_datetime" class="form-label">Payment Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="payment_datetime" id="payment_datetime" class="form-control @error('payment_datetime') is-invalid @enderror" value="{{ old('payment_datetime', now()->format('Y-m-d\TH:i')) }}" required>
                                 @error('payment_datetime') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="payment_proof" class="form-label">Payment Proof (Screenshot/Receipt - Max 2MB) <span class="text-danger">*</span></label>
                                <input type="file" name="payment_proof" id="payment_proof" class="form-control @error('payment_proof') is-invalid @enderror" required accept="image/jpeg,image/png,application/pdf">
                                @error('payment_proof')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-check-circle"></i> Submit Payment Proof
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts') {{-- Or @section('scripts') if not using stacks --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const instructionsDisplay = document.getElementById('payment_instructions_display');

    function displayInstructions() {
        const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
        const instructions = selectedOption.getAttribute('data-instructions');

        if (instructions && instructions.trim() !== '') {
            instructionsDisplay.innerHTML = '<h6 class="alert-heading">Payment Instructions:</h6><p class="mb-0">' + instructions.replace(/\n/g, '<br>') + '</p>';
            instructionsDisplay.style.display = 'block';
        } else {
            instructionsDisplay.style.display = 'none';
        }
    }

    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', displayInstructions);
        // Initial display if a method is pre-selected (e.g., from old input)
        if (paymentMethodSelect.value) {
            displayInstructions();
        }
    }
});
</script>
@endpush
