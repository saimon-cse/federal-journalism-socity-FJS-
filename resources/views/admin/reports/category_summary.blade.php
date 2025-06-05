@extends('layouts.admin.app')

@section('title', 'Transaction Category Summary')
@section('page-title', 'Category Summary Report')

@section('content')
    @include('admin.reports.partials._filters', [
        'filterActionUrl' => route('admin.reports.category-summary'),
        'showTypeFilter' => true, // Pass true to show the type filter
        'types' => $types,         // Pass the array of types ['income', 'expense']
        'typeFilter' => $typeFilter // Pass the currently selected type filter
    ])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Summary by Category for period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                @if($typeFilter) ({{ ucfirst($typeFilter) }} Only) @endif
            </h3>
        </div>
        <div class="card-body">
            @if($categorySummaries->isEmpty())
                <p class="text-muted">No transactions found for the selected criteria.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Type</th>
                                <th class="text-right">Total Amount (BDT)</th>
                                <th class="text-center">Transaction Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotalIncome = 0;
                                $grandTotalExpense = 0;
                            @endphp
                            @foreach($categorySummaries as $summary)
                                @if($summary->category_type == 'income') @php $grandTotalIncome += $summary->total_amount; @endphp
                                @elseif($summary->category_type == 'expense') @php $grandTotalExpense += $summary->total_amount; @endphp
                                @endif
                            <tr>
                                <td>{{ $summary->category_name }}</td>
                                <td>
                                    @if($summary->category_type == 'income')
                                        <span class="badge badge-success">Income</span>
                                    @elseif($summary->category_type == 'expense')
                                        <span class="badge badge-danger">Expense</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($summary->category_type == 'income')
                                        <span class="text-success">{{ number_format($summary->total_amount, 2) }}</span>
                                    @elseif($summary->category_type == 'expense')
                                        <span class="text-danger">{{ number_format($summary->total_amount, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $summary->transaction_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        @if(!$typeFilter) {{-- Show grand totals only if not filtering by a single type --}}
                        <tfoot class="table-light font-weight-bold">
                            <tr>
                                <td colspan="2" class="text-right">Total Income:</td>
                                <td class="text-right text-success">{{ number_format($grandTotalIncome, 2) }}</td>
                                <td></td>
                            </tr>
                             <tr>
                                <td colspan="2" class="text-right">Total Expense:</td>
                                <td class="text-right text-danger">{{ number_format($grandTotalExpense, 2) }}</td>
                                <td></td>
                            </tr>
                             <tr>
                                <td colspan="2" class="text-right">Net:</td>
                                <td class="text-right {{ ($grandTotalIncome - $grandTotalExpense >=0) ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($grandTotalIncome - $grandTotalExpense, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
