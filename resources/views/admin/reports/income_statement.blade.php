@extends('layouts.admin.app')

@section('title', 'Income Statement')
@section('page-title', 'Income Statement')

@section('header-actions')
    {{-- Existing filter form --}}
    @include('admin.reports.partials._filters', ['filterActionUrl' => route('admin.reports.income-statement')])

    <div class="ml-auto"> {{-- Push export buttons to the right --}}
        <a href="{{ route('admin.reports.income-statement.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>
        <a href="{{ route('admin.reports.income-statement.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
    </div>
@endsection

@section('content')
    @include('admin.reports.partials._filters', ['filterActionUrl' => route('admin.reports.income-statement')])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Income Statement for period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Revenue</h4>
                    @if($revenueDetails->count() > 0)
                        <table class="table table-sm table-hover">
                            <tbody>
                                @php $totalRevenue = 0; @endphp
                                @foreach($revenueDetails as $categoryName => $amount)
                                <tr>
                                    <td>{{ $categoryName }}</td>
                                    <td class="text-right">{{ number_format($amount, 2) }}</td>
                                </tr>
                                @php $totalRevenue += $amount; @endphp
                                @endforeach
                                <tr class="font-weight-bold table-light">
                                    <td>Total Revenue</td>
                                    <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No revenue recorded for this period.</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4>Expenses</h4>
                     @if($expenseDetails->count() > 0)
                        <table class="table table-sm table-hover">
                            <tbody>
                                @php $totalExpenses = 0; @endphp
                                @foreach($expenseDetails as $categoryName => $amount)
                                <tr>
                                    <td>{{ $categoryName }}</td>
                                    <td class="text-right">{{ number_format($amount, 2) }}</td>
                                </tr>
                                @php $totalExpenses += $amount; @endphp
                                @endforeach
                                <tr class="font-weight-bold table-light">
                                    <td>Total Expenses</td>
                                    <td class="text-right">{{ number_format($totalExpenses, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No expenses recorded for this period.</p>
                    @endif
                </div>
            </div>

            <hr class="my-4">
            @if(isset($totalRevenue) && isset($totalExpenses))
                @php $netIncome = $totalRevenue - $totalExpenses; @endphp
                <div class="d-flex justify-content-between font-weight-bold h4 mt-3 p-3 rounded {{ $netIncome >= 0 ? 'bg-success-light text-success-fg' : 'bg-danger-light text-danger-fg' }}">
                    <span>Net {{ $netIncome >= 0 ? 'Income (Profit)' : 'Loss' }}</span>
                    <span>{{ number_format($netIncome, 2) }} BDT</span>
                </div>
            @endif
        </div>
    </div>
@endsection
