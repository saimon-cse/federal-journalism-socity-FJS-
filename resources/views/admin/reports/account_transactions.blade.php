@extends('layouts.admin.app')

@section('title', 'Account Transactions - ' . ($paymentAccount->account_name ?? 'Select Account'))
@section('page-title')
    Transactions for: <span class="text-primary">{{ $paymentAccount->account_name ?? 'N/A' }}</span>
@endsection

@section('content')
    @include('admin.reports.partials._filters', [
        'filterActionUrl' => $paymentAccount ? route('admin.reports.account-transactions', $paymentAccount->id) : route('admin.reports.account-transactions', ['paymentAccount' => 'temp']), // Temporary for URL generation
        'showAccountFilter' => true,
        'baseAccountFilterUrl' => route('admin.reports.account-transactions', ['paymentAccount' => '']) // Base URL for JS redirection
        // Make sure $paymentAccounts is passed to the partial from the controller
    ])

    @if($paymentAccount)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction History for period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Opening Balance:</strong> {{ number_format($openingBalance, 2) }} BDT
                    </div>
                    <div class="col-md-4 text-center">
                        {{-- Net Change could be calculated and shown here --}}
                    </div>
                    <div class="col-md-4 text-right">
                         <strong>Approx. Closing Balance for Period:</strong> {{ number_format($closingBalance, 2) }} BDT <br>
                         <small class="text-muted"><strong>Current Account Balance:</strong> {{ number_format($paymentAccount->current_balance, 2) }} BDT</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th class="text-right">Debit (-)</th>
                                <th class="text-right">Credit (+)</th>
                                <th class="text-right">Running Balance</th> {{-- More complex to calculate on the fly per page --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBalance = $openingBalance; @endphp
                            @forelse ($transactions as $transaction)
                                @php
                                    $isDebit = ($transaction->from_payment_account_id == $paymentAccount->id && in_array($transaction->entry_type, ['expense', 'transfer']));
                                    $isCredit = ($transaction->to_payment_account_id == $paymentAccount->id && in_array($transaction->entry_type, ['income', 'transfer', 'opening_balance']));
                                    if ($isCredit) $runningBalance += $transaction->amount;
                                    if ($isDebit) $runningBalance -= $transaction->amount;
                                @endphp
                                <tr>
                                    <td>{{ $transaction->transaction_datetime->format('d M Y H:i') }}</td>
                                    <td style="white-space: normal;">
                                        {{ $transaction->description }}
                                        @if($transaction->entry_type == 'transfer')
                                            <br><small class="text-muted">
                                                @if($isDebit)
                                                    To: {{ $transaction->toPaymentAccount->account_name ?? 'External' }}
                                                @elseif($isCredit)
                                                    From: {{ $transaction->fromPaymentAccount->account_name ?? 'External' }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->category->name ?? ($transaction->entry_type == 'transfer' ? 'Transfer' : 'N/A') }}</td>
                                    <td class="text-right text-danger">
                                        @if($isDebit)
                                            {{ number_format($transaction->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right text-success">
                                        @if($isCredit)
                                            {{ number_format($transaction->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No transactions found for this account in the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                         <tfoot>
                            <tr class="table-light font-weight-bold">
                                <td colspan="5" class="text-right">Calculated Closing Balance for Period:</td>
                                <td class="text-right">{{ number_format($runningBalance, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">Please select a payment account to view its transaction history.</div>
    @endif
@endsection
