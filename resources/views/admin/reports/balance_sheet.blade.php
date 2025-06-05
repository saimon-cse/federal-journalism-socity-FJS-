@extends('layouts.admin.app')

@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet')

@section('header-actions')
    <form method="GET" action="{{ route('admin.reports.balance-sheet') }}" class="form-inline">
        <div class="form-group mr-2">
            <label for="as_of_date" class="mr-1">As of Date:</label>
            <input type="date" name="as_of_date" id="as_of_date" class="form-control form-control-sm" value="{{ $asOfDate->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
        </div>
        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> View Report</button>
        <a href="{{ route('admin.reports.balance-sheet') }}" class="btn btn-sm btn-outline-secondary ml-2">Current Date</a>
        {{-- Export Buttons will go here --}}
        <a href="{{ route('admin.reports.balance-sheet.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-success ml-3"><i class="fas fa-file-csv"></i> Export CSV</a>
        <a href="{{ route('admin.reports.balance-sheet.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-sm btn-danger ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
    </form>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Balance Sheet <small class="text-muted">as of {{ $asOfDate->format('F d, Y') }}</small></h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- ASSETS SECTION --}}
                <div class="col-md-6 border-right">
                    <h4 class="text-primary mb-3">Assets</h4>

                    <h5 class="mt-3 mb-2">Current Assets</h5>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <td class="pl-3 font-weight-bold">Cash and Cash Equivalents</td>
                                <td></td> {{-- Subtotal for this category if needed --}}
                            </tr>
                            @php $totalCashAssets = 0; @endphp
                            @forelse($assets as $asset)
                                <tr>
                                    <td class="pl-5">{{ $asset->account_name }} ({{ $asset->account_provider }})</td>
                                    <td class="text-right">{{ number_format($asset->current_balance, 2) }}</td>
                                </tr>
                                @php $totalCashAssets += $asset->current_balance; @endphp
                            @empty
                                <tr>
                                    <td colspan="2" class="pl-5 text-muted">No cash accounts with balance.</td>
                                </tr>
                            @endforelse
                            <tr class="font-weight-bold border-top">
                                <td class="pl-3">Total Cash and Cash Equivalents</td>
                                <td class="text-right">{{ number_format($totalCashAssets, 2) }}</td>
                            </tr>

                            {{-- Placeholder for other current assets like Accounts Receivable if implemented --}}
                            {{--
                            <tr>
                                <td class="pl-3 font-weight-bold">Accounts Receivable</td>
                                <td class="text-right">0.00</td>
                            </tr>
                            --}}
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="font-weight-bold h5">
                                <td>Total Current Assets</td>
                                <td class="text-right">{{ number_format($totalAssets, 2) }}</td> {{-- Assuming totalAssets is primarily current for now --}}
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Placeholder for Non-Current Assets if implemented --}}
                    {{-- <h5 class="mt-4 mb-2">Non-Current Assets</h5> ... --}}

                    <hr class="my-4 d-md-none"> {{-- Separator for mobile view --}}
                </div>

                {{-- LIABILITIES AND EQUITY SECTION --}}
                <div class="col-md-6">
                    <h4 class="text-danger mb-3">Liabilities</h4>
                    {{-- Placeholder for Current Liabilities --}}
                    <h5 class="mt-3 mb-2">Current Liabilities</h5>
                     @if($liabilities->count() > 0)
                        <table class="table table-sm table-borderless">
                           <tbody>
                                {{-- Loop through liabilities --}}
                           </tbody>
                        </table>
                    @else
                        <p class="text-muted pl-3">No current liabilities tracked.</p>
                    @endif
                    <div class="d-flex justify-content-between font-weight-bold p-2 mt-2 table-light">
                        <span>Total Current Liabilities</span>
                        <span class="text-right">{{ number_format($totalLiabilities, 2) }}</span>
                    </div>

                    {{-- Placeholder for Non-Current Liabilities --}}
                    {{-- <h5 class="mt-4 mb-2">Non-Current Liabilities</h5> ... --}}
                    <div class="d-flex justify-content-between font-weight-bold p-2 mt-2 table-light h5">
                        <span>Total Liabilities</span>
                        <span class="text-right">{{ number_format($totalLiabilities, 2) }}</span>
                    </div>

                    <hr class="my-3">

                    <h4 class="text-success mb-3">Equity</h4>
                    {{-- Placeholder for Equity components --}}
                    @if($equity->count() > 0)
                        <table class="table table-sm table-borderless">
                           <tbody>
                                {{-- Loop through equity components like 'Retained Earnings', 'Owner Contribution' --}}
                                 <tr>
                                    <td class="pl-3">Retained Earnings (Calculated)</td>
                                    <td class="text-right">{{ number_format($totalEquity, 2) }}</td> {{-- Simplified: all equity is retained earnings --}}
                                </tr>
                           </tbody>
                        </table>
                    @else
                         <p class="text-muted pl-3">Equity is calculated as (Total Assets - Total Liabilities).</p>
                         <div class="d-flex justify-content-between p-2 mt-2">
                            <span>Retained Earnings / Net Position</span>
                            <span class="text-right">{{ number_format($totalEquity, 2) }}</span>
                        </div>
                    @endif
                     <div class="d-flex justify-content-between font-weight-bold p-2 mt-2 table-light h5">
                        <span>Total Equity</span>
                        <span class="text-right">{{ number_format($totalEquity, 2) }}</span>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between font-weight-bold h4 p-3 rounded bg-primary-light text-primary-fg">
                        <span>Total Assets</span>
                        <span>{{ number_format($totalAssets, 2) }} BDT</span>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="d-flex justify-content-between font-weight-bold h4 p-3 rounded {{ abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01  ? 'bg-primary-light text-primary-fg' : 'bg-warning-light text-warning-fg' }}">
                        <span>Total Liabilities & Equity</span>
                        <span>{{ number_format($totalLiabilities + $totalEquity, 2) }} BDT</span>
                    </div>
                </div>
            </div>

            @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) >= 0.01)
                <div class="alert alert-warning mt-3">
                    <strong>Balance Check:</strong> Assets do not perfectly equal Liabilities + Equity (Difference: {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }}). This indicates this simplified balance sheet may not capture all financial elements or there's a calculation discrepancy. A full chart of accounts is needed for a comprehensive balance sheet.
                </div>
            @else
                 <div class="alert alert-success mt-3">
                    <strong>Balance Check:</strong> Assets = Liabilities + Equity.
                </div>
            @endif
        </div>
    </div>
@endsection
