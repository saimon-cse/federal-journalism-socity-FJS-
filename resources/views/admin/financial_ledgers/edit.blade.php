@extends('layouts.admin.app')

@section('title', 'Edit Ledger Entry')
@section('page-title')
    Edit Ledger: <span class="text-primary">{{ $financialLedger->ledger_entry_uuid }}</span>
@endsection

@section('header-actions')
    <a href="{{ route('admin.financial-ledgers.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Back to Ledger
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Ledger Entry Details</h3>
        </div>
        <form action="{{ route('admin.financial-ledgers.update', $financialLedger->id) }}" method="POST" id="ledgerEntryForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="alert alert-danger">
                    <strong>Warning:</strong> Editing historical financial records should be done with extreme caution. This will attempt to reverse old balance changes and apply new ones. Ensure data accuracy.
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transaction_datetime">Transaction Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('transaction_datetime') is-invalid @enderror" id="transaction_datetime" name="transaction_datetime" value="{{ old('transaction_datetime', $financialLedger->transaction_datetime->format('Y-m-d\TH:i')) }}" required>
                            @error('transaction_datetime') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="entry_type">Entry Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('entry_type') is-invalid @enderror" id="entry_type" name="entry_type" required>
                                <option value="income" {{ old('entry_type', $financialLedger->entry_type) == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('entry_type', $financialLedger->entry_type) == 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="transfer" {{ old('entry_type', $financialLedger->entry_type) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="opening_balance" {{ old('entry_type', $financialLedger->entry_type) == 'opening_balance' ? 'selected' : '' }}>Opening Balance</option>
                                <option value="reconciliation_adjustment" {{ old('entry_type', $financialLedger->entry_type) == 'reconciliation_adjustment' ? 'selected' : '' }}>Reconciliation Adjustment</option>
                            </select>
                            @error('entry_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount">Amount (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $financialLedger->amount) }}" required>
                            @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $financialLedger->description) }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                {{-- Conditional Fields --}}
                <div id="income_expense_fields" style="display: none;">
                    <div class="form-group">
                        <label for="category_id">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id_dup">
                            {{-- JS will populate and set the correct 'name' --}}
                        </select>
                        @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div id="income_fields" style="display: none;">
                     <div class="form-group">
                        <label for="to_payment_account_id_income">To Account (Received In) <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_payment_account_id') is-invalid @enderror" id="to_payment_account_id_income" name="to_payment_account_id_income_dup">
                            <option value="">Select Account</option>
                            @foreach($paymentAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('to_payment_account_id', $financialLedger->to_payment_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{$account->account_provider}})
                                </option>
                            @endforeach
                        </select>
                        @error('to_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div id="expense_fields" style="display: none;">
                    <div class="form-group">
                        <label for="from_payment_account_id_expense">From Account (Paid From) <span class="text-danger">*</span></label>
                        <select class="form-select @error('from_payment_account_id') is-invalid @enderror" id="from_payment_account_id_expense" name="from_payment_account_id_expense_dup">
                            <option value="">Select Account</option>
                             @foreach($paymentAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('from_payment_account_id', $financialLedger->from_payment_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{$account->account_provider}})
                                </option>
                            @endforeach
                        </select>
                        @error('from_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div id="transfer_fields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_payment_account_id_transfer">Transfer From Account <span class="text-danger">*</span></label>
                                <select class="form-select @error('from_payment_account_id') is-invalid @enderror" id="from_payment_account_id_transfer" name="from_payment_account_id_transfer_dup">
                                     <option value="">Select Account</option>
                                    @foreach($paymentAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('from_payment_account_id', $financialLedger->from_payment_account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_name }} ({{$account->account_provider}})
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_payment_account_id_transfer">Transfer To Account <span class="text-danger">*</span></label>
                                <select class="form-select @error('to_payment_account_id') is-invalid @enderror" id="to_payment_account_id_transfer" name="to_payment_account_id_transfer_dup">
                                    <option value="">Select Account</option>
                                     @foreach($paymentAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('to_payment_account_id', $financialLedger->to_payment_account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_name }} ({{$account->account_provider}})
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="opening_balance_fields" style="display: none;">
                     <div class="form-group">
                        <label for="to_payment_account_id_opening">Account for Opening Balance <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_payment_account_id') is-invalid @enderror" id="to_payment_account_id_opening" name="to_payment_account_id_opening_dup">
                            <option value="">Select Account</option>
                            @foreach($paymentAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('to_payment_account_id', $financialLedger->to_payment_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{$account->account_provider}})
                                </option>
                            @endforeach
                        </select>
                        @error('to_payment_account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>


                <h5 class="mt-4 mb-3">Additional Details (Optional)</h5>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="external_party_name">External Party Name</label>
                            <input type="text" class="form-control @error('external_party_name') is-invalid @enderror" id="external_party_name" name="external_party_name" value="{{ old('external_party_name', $financialLedger->external_party_name) }}">
                            @error('external_party_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="external_reference_id">External Reference ID</label>
                            <input type="text" class="form-control @error('external_reference_id') is-invalid @enderror" id="external_reference_id" name="external_reference_id" value="{{ old('external_reference_id', $financialLedger->external_reference_id) }}">
                            @error('external_reference_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="internal_notes">Internal Notes</label>
                    <textarea class="form-control @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes', $financialLedger->internal_notes) }}</textarea>
                    @error('internal_notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Entry
                </button>
                <a href="{{ route('admin.financial-ledgers.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const entryTypeSelect = document.getElementById('entry_type');
    const incomeExpenseFields = document.getElementById('income_expense_fields');
    const incomeFields = document.getElementById('income_fields');
    const expenseFields = document.getElementById('expense_fields');
    const transferFields = document.getElementById('transfer_fields');
    const openingBalanceFields = document.getElementById('opening_balance_fields');

    const categorySelect = document.getElementById('category_id');
    const fromAccountExpenseSelect = document.getElementById('from_payment_account_id_expense');
    const toAccountIncomeSelect = document.getElementById('to_payment_account_id_income');
    const fromAccountTransferSelect = document.getElementById('from_payment_account_id_transfer');
    const toAccountTransferSelect = document.getElementById('to_payment_account_id_transfer');
    const toAccountOpeningSelect = document.getElementById('to_payment_account_id_opening');

    const incomeCategories = @json($incomeCategories ?? []); // Pass these from controller's edit method
    const expenseCategories = @json($expenseCategories ?? []); // Pass these from controller's edit method
    const selectedCategoryId = "{{ old('category_id', $financialLedger->category_id) }}";


    function updateCategoryOptions(type) {
        let options = '<option value="">Select Category</option>';
        let categoriesToUse = [];
        if (type === 'income') categoriesToUse = incomeCategories;
        else if (type === 'expense') categoriesToUse = expenseCategories;

        categoriesToUse.forEach(cat => {
            options += `<option value="${cat.id}" ${selectedCategoryId == cat.id ? 'selected' : ''}>${cat.name}</option>`;
        });
        categorySelect.innerHTML = options;
    }

    function toggleFields() {
        const selectedType = entryTypeSelect.value;

        incomeExpenseFields.style.display = 'none';
        incomeFields.style.display = 'none';
        expenseFields.style.display = 'none';
        transferFields.style.display = 'none';
        openingBalanceFields.style.display = 'none';

        categorySelect.name = 'category_id_dup';
        fromAccountExpenseSelect.name = 'from_payment_account_id_expense_dup';
        toAccountIncomeSelect.name = 'to_payment_account_id_income_dup';
        fromAccountTransferSelect.name = 'from_payment_account_id_transfer_dup';
        toAccountTransferSelect.name = 'to_payment_account_id_transfer_dup';
        toAccountOpeningSelect.name = 'to_payment_account_id_opening_dup';

        if (selectedType === 'income') {
            incomeExpenseFields.style.display = 'block';
            incomeFields.style.display = 'block';
            updateCategoryOptions('income');
            categorySelect.name = 'category_id';
            toAccountIncomeSelect.name = 'to_payment_account_id';
        } else if (selectedType === 'expense') {
            incomeExpenseFields.style.display = 'block';
            expenseFields.style.display = 'block';
            updateCategoryOptions('expense');
            categorySelect.name = 'category_id';
            fromAccountExpenseSelect.name = 'from_payment_account_id';
        } else if (selectedType === 'transfer') {
            transferFields.style.display = 'block';
            fromAccountTransferSelect.name = 'from_payment_account_id';
            toAccountTransferSelect.name = 'to_payment_account_id';
        } else if (selectedType === 'opening_balance') {
            openingBalanceFields.style.display = 'block';
            toAccountOpeningSelect.name = 'to_payment_account_id';
        }
        // For reconciliation_adjustment, no specific fields are shown by default here
    }

    if(entryTypeSelect) {
        entryTypeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial call
    }
});
</script>
@endsection
