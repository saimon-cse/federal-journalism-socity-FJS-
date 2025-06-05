<form method="GET" action="{{ $filterActionUrl }}" class="form-inline mb-4 flex-wrap">
    @if(isset($showAccountFilter) && $showAccountFilter)
    <div class="form-group mr-2 mb-2">
        <label for="payment_account_id_filter" class="mr-1">Account:</label>
        <select name="payment_account_id" id="payment_account_id_filter" class="form-select form-select-sm" onchange="this.form.action = '{{ $baseAccountFilterUrl }}/' + this.value; this.form.submit()">
            <option value="">Select Account to View</option>
            @foreach($paymentAccounts ?? [] as $acc)
                <option value="{{ $acc->id }}" {{ (isset($paymentAccount) && $paymentAccount->id == $acc->id) || request('payment_account_id') == $acc->id ? 'selected' : '' }}>
                    {{ $acc->account_name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    @if(isset($showTypeFilter) && $showTypeFilter)
    <div class="form-group mr-2 mb-2">
        <label for="type_filter" class="mr-1">Type:</label>
        <select name="type" id="type_filter" class="form-select form-select-sm">
            <option value="">All Types</option>
            @foreach($types ?? [] as $typeValue)
                <option value="{{ $typeValue }}" {{ $typeFilter == $typeValue ? 'selected' : '' }}>
                    {{ ucfirst($typeValue) }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="form-group mr-2 mb-2">
        <label for="start_date" class="mr-1">From:</label>
        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate->format('Y-m-d') }}">
    </div>
    <div class="form-group mr-2 mb-2">
        <label for="end_date" class="mr-1">To:</label>
        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate->format('Y-m-d') }}">
    </div>
    <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fas fa-filter"></i> Filter</button>
    <a href="{{ $filterActionUrl }}" class="btn btn-sm btn-outline-secondary ml-2 mb-2">Reset</a>
</form>
