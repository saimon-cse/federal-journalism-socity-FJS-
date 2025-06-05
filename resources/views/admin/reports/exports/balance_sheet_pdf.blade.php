@php
    use App\Models\Setting;
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet</title>
    <style>
        /* ... (similar CSS as income_statement_pdf.blade.php, adjust as needed) ... */
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; } .header p { margin: 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; border-bottom: 1px solid #666; padding-bottom: 3px;}
        .category-total td { font-weight: bold; background-color: #f8f8f8; border-top: 1px solid #999;}
        .grand-total td { font-weight: bold; font-size: 12px; background-color: #e0e0e0; border-top: 2px solid #333;}
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ Setting::get('site_name', config('app.name')) }}</h1>
        <p>Balance Sheet</p>
        <p>As of: {{ $asOfDate->format('F d, Y') }}</p>
    </div>

    <div class="section-title">Assets</div>
    <table>
        <thead><tr><th>Account</th><th class="text-right">Amount (BDT)</th></tr></thead>
        <tbody>
            <tr><td colspan="2"><strong>Current Assets</strong></td></tr>
            <tr><td colspan="2" style="padding-left: 15px;"><em>Cash and Cash Equivalents</em></td></tr>
            @forelse($assets as $asset)
            <tr>
                <td style="padding-left: 30px;">{{ $asset->account_name }} ({{ $asset->account_provider }})</td>
                <td class="text-right">{{ number_format($asset->current_balance, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="padding-left: 30px;">No cash assets.</td></tr>
            @endforelse
            <tr class="category-total">
                <td style="padding-left: 15px;">Total Cash and Cash Equivalents</td>
                <td class="text-right">{{ number_format($assets->sum('current_balance'), 2) }}</td>
            </tr>
            {{-- Other Current Assets placeholder --}}
        </tbody>
        <tfoot class="grand-total">
            <tr><td>Total Assets</td><td class="text-right">{{ number_format($totalAssets, 2) }}</td></tr>
        </tfoot>
    </table>

    <div class="section-title">Liabilities</div>
    <table>
         <thead><tr><th>Account</th><th class="text-right">Amount (BDT)</th></tr></thead>
        <tbody>
            {{-- Current Liabilities placeholder --}}
            <tr><td colspan="2"><strong>Current Liabilities</strong></td></tr>
            @if($liabilities->isEmpty())
            <tr><td colspan="2" style="padding-left: 15px;">No liabilities tracked.</td></tr>
            @else
                {{-- Loop through liabilities --}}
            @endif
        </tbody>
        <tfoot class="grand-total">
            <tr><td>Total Liabilities</td><td class="text-right">{{ number_format($totalLiabilities, 2) }}</td></tr>
        </tfoot>
    </table>

    <div class="section-title">Equity</div>
    <table>
        <thead><tr><th>Account</th><th class="text-right">Amount (BDT)</th></tr></thead>
        <tbody>
            @foreach($equity as $eq)
            <tr>
                <td style="padding-left: 15px;">{{ $eq['name'] }}</td>
                <td class="text-right">{{ number_format($eq['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="grand-total">
            <tr><td>Total Equity</td><td class="text-right">{{ number_format($totalEquity, 2) }}</td></tr>
        </tfoot>
    </table>

    <hr style="margin: 20px 0; border-style: dashed;">
     <table>
        <tr class="grand-total" style="background-color: #cce5ff;">
            <td>Total Liabilities & Equity</td>
            <td class="text-right">{{ number_format($totalLiabilities + $totalEquity, 2) }}</td>
        </tr>
    </table>

</body>
</html>
