@extends('layouts.admin.app')

@section('title', 'Cash Flow Statement')
@section('page-title', 'Cash Flow Statement')

@section('content')
    @include('admin.reports.partials._filters', ['filterActionUrl' => route('admin.reports.cash-flow')])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cash Flow for period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h3>
        </div>
        <div class="card-body">
            {{-- Simplified Cash Flow - ideally categorize into Operating, Investing, Financing --}}

            <h5 class="text-success">Cash Inflows</h5>
            @if($cashInflows->count() > 0)
            <table class="table table-sm table-borderless">
                <tbody>
                    @foreach($cashInflows as $categoryName => $amount)
                    <tr>
                        <td>{{ $categoryName }}</td>
                        <td class="text-right">{{ number_format($amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="font-weight-bold border-top">
                        <td>Total Cash Inflows</td>
                        <td class="text-right">{{ number_format($totalInflows, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            @else
            <p class="text-muted">No cash inflows recorded for this period.</p>
            @endif

            <h5 class="mt-4 text-danger">Cash Outflows</h5>
            @if($cashOutflows->count() > 0)
            <table class="table table-sm table-borderless">
                <tbody>
                    @foreach($cashOutflows as $categoryName => $amount)
                    <tr>
                        <td>{{ $categoryName }}</td>
                        <td class="text-right">({{ number_format($amount, 2) }})</td>
                    </tr>
                    @endforeach
                    <tr class="font-weight-bold border-top">
                        <td>Total Cash Outflows</td>
                        <td class="text-right">({{ number_format($totalOutflows, 2) }})</td>
                    </tr>
                </tbody>
            </table>
            @else
            <p class="text-muted">No cash outflows recorded for this period.</p>
            @endif

            <hr class="my-3">

            <table class="table table-sm table-borderless">
                <tbody>
                    {{--
                    <tr>
                        <td>Beginning Cash Balance (Approximation)</td>
                        <td class="text-right">{{ number_format($beginningCashBalance, 2) }}</td>
                    </tr>
                    --}}
                    <tr class="font-weight-bold h5">
                        <td>Net Cash Flow for Period</td>
                        <td class="text-right {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($netCashFlow, 2) }}
                        </td>
                    </tr>
                    {{--
                    <tr class="font-weight-bold table-light">
                        <td>Ending Cash Balance (Approximation)</td>
                        <td class="text-right">{{ number_format($endingCashBalance, 2) }}</td>
                    </tr>
                     --}}
                </tbody>
            </table>
            <small class="text-muted">Note: This is a simplified cash flow statement. Full statement requires categorization into operating, investing, and financing activities and precise beginning/ending balances.</small>

        </div>
    </div>
@endsection
