@php
    use App\Models\Setting;
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Income Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background-color: #f9f9f9; }
        .net-income { font-weight: bold; font-size: 14px; padding: 10px; border-top: 2px solid #333; border-bottom: 2px solid #333;}
        .text-success { color: green; }
        .text-danger { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ Setting::get('site_name', config('app.name')) }}</h1>
        <p>Income Statement</p>
        <p>For the period: {{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</p>
    </div>

    <h4>Revenue</h4>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRevenue = 0; @endphp
            @forelse($revenueDetails as $categoryName => $amount)
            <tr>
                <td>{{ $categoryName }}</td>
                <td class="text-right">{{ number_format($amount, 2) }}</td>
            </tr>
            @php $totalRevenue += $amount; @endphp
            @empty
            <tr><td colspan="2">No revenue recorded.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="total-row">
            <tr>
                <td>Total Revenue</td>
                <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h4>Expenses</h4>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalExpenses = 0; @endphp
            @forelse($expenseDetails as $categoryName => $amount)
            <tr>
                <td>{{ $categoryName }}</td>
                <td class="text-right">{{ number_format($amount, 2) }}</td>
            </tr>
            @php $totalExpenses += $amount; @endphp
            @empty
            <tr><td colspan="2">No expenses recorded.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="total-row">
            <tr>
                <td>Total Expenses</td>
                <td class="text-right">{{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @php $netIncome = $totalRevenue - $totalExpenses; @endphp
    <div class="net-income">
        Net {{ $netIncome >= 0 ? 'Income (Profit)' : 'Loss' }}:
        <span class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}" style="float:right;">
            {{ number_format($netIncome, 2) }} BDT
        </span>
    </div>
</body>
</html>
