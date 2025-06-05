<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedger;
use App\Models\FinancialTransactionCategory;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer as CsvWriter; // For CSV
use Barryvdh\DomPDF\Facade\Pdf;      // For PDF
use SplTempFileObject;
use Illuminate\Support\Str;
class FinancialReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-financial-reports');
    }

    private function getDateRange(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        return [$startDate, $endDate];
    }

     // --- Income Statement ---
    private function getIncomeStatementData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        $revenueDetails = FinancialLedger::where('entry_type', 'income')
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->join('financial_transaction_categories', 'financial_ledgers.category_id', '=', 'financial_transaction_categories.id')
            ->select('financial_transaction_categories.name as category_name', DB::raw('SUM(financial_ledgers.amount) as total_amount'))
            ->groupBy('financial_transaction_categories.name')
            ->orderBy('category_name')
            ->pluck('total_amount', 'category_name');

        $expenseDetails = FinancialLedger::where('entry_type', 'expense')
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->join('financial_transaction_categories', 'financial_ledgers.category_id', '=', 'financial_transaction_categories.id')
            ->select('financial_transaction_categories.name as category_name', DB::raw('SUM(financial_ledgers.amount) as total_amount'))
            ->groupBy('financial_transaction_categories.name')
            ->orderBy('category_name')
            ->pluck('total_amount', 'category_name');

        return compact('revenueDetails', 'expenseDetails', 'startDate', 'endDate');
    }

    public function incomeStatement(Request $request)
    {
        $data = $this->getIncomeStatementData($request);
        return view('admin.reports.income_statement', $data);
    }

    public function exportIncomeStatement(Request $request)
    {
        $data = $this->getIncomeStatementData($request);
        $format = $request->query('format', 'csv'); // Default to CSV
        $filename = 'income-statement-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.income_statement_pdf', $data);
            return $pdf->download($filename . '.pdf');
        } else { // CSV
            $csv = CsvWriter::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['Income Statement: ' . $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            $csv->insertOne([]); // Empty row
            $csv->insertOne(['Category Type', 'Category Name', 'Amount (BDT)']);

            $totalRevenue = 0;
            $csv->insertOne(['REVENUE']);
            foreach ($data['revenueDetails'] as $category => $amount) {
                $csv->insertOne(['Revenue', $category, $amount]);
                $totalRevenue += $amount;
            }
            $csv->insertOne(['', 'Total Revenue', $totalRevenue]);
            $csv->insertOne([]);

            $totalExpenses = 0;
            $csv->insertOne(['EXPENSES']);
            foreach ($data['expenseDetails'] as $category => $amount) {
                $csv->insertOne(['Expense', $category, $amount]);
                $totalExpenses += $amount;
            }
            $csv->insertOne(['', 'Total Expenses', $totalExpenses]);
            $csv->insertOne([]);

            $netIncome = $totalRevenue - $totalExpenses;
            $csv->insertOne(['', 'Net ' . ($netIncome >= 0 ? 'Income' : 'Loss'), $netIncome]);

            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        }
    }

// --- Balance Sheet ---
    private function getBalanceSheetData(Request $request)
    {
        $asOfDate = $request->input('as_of_date') ? Carbon::parse($request->input('as_of_date'))->endOfDay() : Carbon::now()->endOfDay();
        $assets = PaymentAccount::where('is_active', true)
            ->select('account_name', 'current_balance', 'account_provider')
            ->orderBy('account_name')
            ->get();
        $totalAssets = $assets->sum('current_balance');
        $liabilities = collect([]);
        $totalLiabilities = 0;
        $equity = collect([['name' => 'Retained Earnings / Net Position (Calculated)', 'amount' => $totalAssets - $totalLiabilities]]);
        $totalEquity = $totalAssets - $totalLiabilities;

        return compact('assets', 'totalAssets', 'liabilities', 'totalLiabilities', 'equity', 'totalEquity', 'asOfDate');
    }

    public function balanceSheet(Request $request)
    {
        $data = $this->getBalanceSheetData($request);
        return view('admin.reports.balance_sheet', $data);
    }

    public function exportBalanceSheet(Request $request)
    {
        $data = $this->getBalanceSheetData($request);
        $format = $request->query('format', 'csv');
        $filename = 'balance-sheet-as-of-' . $data['asOfDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.balance_sheet_pdf', $data);
            return $pdf->download($filename . '.pdf');
        } else { // CSV
            $csv = CsvWriter::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['Balance Sheet as of: ' . $data['asOfDate']->format('F d, Y')]);
            $csv->insertOne([]);
            $csv->insertOne(['ASSETS', '', 'Amount (BDT)']);
            $csv->insertOne(['Current Assets']);
            $csv->insertOne(['', 'Cash and Cash Equivalents']);
            foreach ($data['assets'] as $asset) {
                $csv->insertOne(['', '', $asset->account_name . ' (' . $asset->account_provider . ')', $asset->current_balance]);
            }
            $csv->insertOne(['', 'Total Cash and Cash Equivalents', '', $data['assets']->sum('current_balance')]);
            $csv->insertOne(['Total Current Assets', '', '', $data['totalAssets']]);
            $csv->insertOne(['TOTAL ASSETS', '', '', $data['totalAssets']]);
            $csv->insertOne([]);

            $csv->insertOne(['LIABILITIES & EQUITY']);
            $csv->insertOne(['LIABILITIES']);
            // Add liabilities loop if implemented
            $csv->insertOne(['Total Liabilities', '', '', $data['totalLiabilities']]);
            $csv->insertOne([]);
            $csv->insertOne(['EQUITY']);
            foreach($data['equity'] as $eq) {
                 $csv->insertOne(['', $eq['name'], '', $eq['amount']]);
            }
            $csv->insertOne(['Total Equity', '', '', $data['totalEquity']]);
            $csv->insertOne([]);
            $csv->insertOne(['TOTAL LIABILITIES & EQUITY', '', '', $data['totalLiabilities'] + $data['totalEquity']]);

            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        }
    }

    // --- Cash Flow --- (Simplified, export similar to Income Statement)
    private function getCashFlowData(Request $request){
        [$startDate, $endDate] = $this->getDateRange($request);
        $cashInflows = FinancialLedger::whereIn('entry_type', ['income', 'opening_balance'])
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->join('financial_transaction_categories', 'financial_ledgers.category_id', '=', 'financial_transaction_categories.id')
            ->select('financial_transaction_categories.name as category_name', DB::raw('SUM(financial_ledgers.amount) as total_amount'))
            ->groupBy('financial_transaction_categories.name')
            ->orderBy('category_name')
            ->pluck('total_amount', 'category_name');

        $cashOutflows = FinancialLedger::where('entry_type', 'expense')
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->join('financial_transaction_categories', 'financial_ledgers.category_id', '=', 'financial_transaction_categories.id')
            ->select('financial_transaction_categories.name as category_name', DB::raw('SUM(financial_ledgers.amount) as total_amount'))
            ->groupBy('financial_transaction_categories.name')
            ->orderBy('category_name')
            ->pluck('total_amount', 'category_name');

        $totalInflows = $cashInflows->sum();
        $totalOutflows = $cashOutflows->sum();
        $netCashFlow = $totalInflows - $totalOutflows;

        return compact('cashInflows', 'cashOutflows', 'totalInflows', 'totalOutflows', 'netCashFlow', 'startDate', 'endDate');
    }
    public function cashFlow(Request $request)
    {
        $data = $this->getCashFlowData($request);
        // For simplicity, not including beginning/ending balance calculation in this basic export controller.
        // $data['beginningCashBalance'] = ...; $data['endingCashBalance'] = ...;
        return view('admin.reports.cash_flow', $data);
    }
    public function exportCashFlow(Request $request)
    {
        $data = $this->getCashFlowData($request);
        $format = $request->query('format', 'csv');
        $filename = 'cash-flow-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.cash_flow_pdf', $data);
            return $pdf->download($filename . '.pdf');
        } else { // CSV
             $csv = CsvWriter::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['Cash Flow Statement: ' . $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            $csv->insertOne([]);
            $csv->insertOne(['Type', 'Category', 'Amount (BDT)']);

            $csv->insertOne(['CASH INFLOWS FROM OPERATING ACTIVITIES']); // Simplified
            foreach ($data['cashInflows'] as $category => $amount) {
                $csv->insertOne(['Inflow', $category, $amount]);
            }
            $csv->insertOne(['', 'Total Cash Inflows', $data['totalInflows']]);
            $csv->insertOne([]);

            $csv->insertOne(['CASH OUTFLOWS FROM OPERATING ACTIVITIES']); // Simplified
            foreach ($data['cashOutflows'] as $category => $amount) {
                $csv->insertOne(['Outflow', $category, $amount]);
            }
            $csv->insertOne(['', 'Total Cash Outflows', $data['totalOutflows']]);
            $csv->insertOne([]);

            $csv->insertOne(['', 'Net Cash Flow for Period', $data['netCashFlow']]);

            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        }
    }

   // --- Account Transactions ---
    private function getAccountTransactionsData(Request $request, PaymentAccount $paymentAccount) {
        [$startDate, $endDate] = $this->getDateRange($request);
        $transactions = FinancialLedger::with(['category', 'recordedByUser'])
            ->where(function ($query) use ($paymentAccount) {
                $query->where('from_payment_account_id', $paymentAccount->id)
                      ->orWhere('to_payment_account_id', $paymentAccount->id);
            })
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->orderBy('transaction_datetime', 'asc') // For running balance calculation
            ->orderBy('id', 'asc')
            ->get(); // Get all for correct running balance calculation in export

        $openingBalance = (float) $paymentAccount->initial_balance;
        $prePeriodInflows = FinancialLedger::where('to_payment_account_id', $paymentAccount->id)
            ->where('transaction_datetime', '<', $startDate)
            ->sum('amount');
        $prePeriodOutflows = FinancialLedger::where('from_payment_account_id', $paymentAccount->id)
            ->where('transaction_datetime', '<', $startDate)
            ->sum('amount');
        $openingBalance += ((float)$prePeriodInflows - (float)$prePeriodOutflows);

        return compact('paymentAccount', 'transactions', 'startDate', 'endDate', 'openingBalance');
    }

    public function accountTransactions(Request $request, PaymentAccount $paymentAccount)
    {
        $data = $this->getAccountTransactionsData($request, $paymentAccount);
        // For paginated view, re-fetch with pagination after getting all for accurate balances if needed
        $data['transactions_paginated'] = FinancialLedger::with(['category', 'recordedByUser'])
            ->where(function ($query) use ($paymentAccount) {
                $query->where('from_payment_account_id', $paymentAccount->id)
                      ->orWhere('to_payment_account_id', $paymentAccount->id);
            })
            ->whereBetween('transaction_datetime', [$data['startDate'], $data['endDate']])
            ->orderBy('transaction_datetime', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25); // For display

        $data['paymentAccounts'] = PaymentAccount::orderBy('account_name')->get();
        return view('admin.reports.account_transactions', $data);
    }
    public function exportAccountTransactions(Request $request, PaymentAccount $paymentAccount)
    {
        $data = $this->getAccountTransactionsData($request, $paymentAccount); // Gets all transactions for period
        $format = $request->query('format', 'csv');
        $filename = 'account-transactions-' . Str::slug($data['paymentAccount']->account_name) . '-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.account_transactions_pdf', $data);
            return $pdf->download($filename . '.pdf');
        } else { // CSV
            $csv = CsvWriter::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['Account Transaction Report for: ' . $data['paymentAccount']->account_name]);
            $csv->insertOne(['Period: ' . $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            $csv->insertOne(['Opening Balance for Period:', $data['openingBalance']]);
            $csv->insertOne([]);
            $csv->insertOne(['Date & Time', 'Description', 'Category', 'Debit (-)', 'Credit (+)', 'Running Balance']);

            $runningBalance = $data['openingBalance'];
            foreach ($data['transactions'] as $transaction) {
                $debit = ''; $credit = '';
                if ($transaction->from_payment_account_id == $data['paymentAccount']->id && in_array($transaction->entry_type, ['expense', 'transfer'])) {
                    $debit = $transaction->amount;
                    $runningBalance -= $transaction->amount;
                }
                if ($transaction->to_payment_account_id == $data['paymentAccount']->id && in_array($transaction->entry_type, ['income', 'transfer', 'opening_balance'])) {
                    $credit = $transaction->amount;
                    $runningBalance += $transaction->amount;
                }
                $csv->insertOne([
                    $transaction->transaction_datetime->format('Y-m-d H:i:s'),
                    $transaction->description,
                    $transaction->category->name ?? ($transaction->entry_type == 'transfer' ? 'Transfer' : 'N/A'),
                    $debit,
                    $credit,
                    $runningBalance
                ]);
            }
            $csv->insertOne(['', '', 'Closing Balance for Period:', '', '', $runningBalance]);

            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        }
    }


   // --- Category Summary ---
    private function getCategorySummaryData(Request $request){
        [$startDate, $endDate] = $this->getDateRange($request);
        $typeFilter = $request->input('type', null);

        $query = FinancialLedger::whereBetween('transaction_datetime', [$startDate, $endDate])
            ->join('financial_transaction_categories', 'financial_ledgers.category_id', '=', 'financial_transaction_categories.id')
            ->select(
                'financial_transaction_categories.name as category_name',
                'financial_transaction_categories.type as category_type',
                DB::raw('SUM(financial_ledgers.amount) as total_amount'),
                DB::raw('COUNT(financial_ledgers.id) as transaction_count')
            )
            ->groupBy('financial_transaction_categories.name', 'financial_transaction_categories.type')
            ->orderBy('financial_transaction_categories.type')
            ->orderBy('category_name');

        if ($typeFilter) {
            $query->where('financial_transaction_categories.type', $typeFilter);
        }
        $categorySummaries = $query->get();
        return compact('categorySummaries', 'startDate', 'endDate', 'typeFilter');
    }
    public function categorySummary(Request $request)
    {
        $data = $this->getCategorySummaryData($request);
        $data['types'] = ['income', 'expense']; // For filter dropdown
        return view('admin.reports.category_summary', $data);
    }
    public function exportCategorySummary(Request $request)
    {
        $data = $this->getCategorySummaryData($request);
        $format = $request->query('format', 'csv');
        $filename = 'category-summary-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd') . ($data['typeFilter'] ? '-'.$data['typeFilter'] : '');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.category_summary_pdf', $data);
            return $pdf->download($filename . '.pdf');
        } else { // CSV
            $csv = CsvWriter::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['Category Summary Report']);
            $csv->insertOne(['Period: ' . $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            if ($data['typeFilter']) {
                $csv->insertOne(['Type Filter: ' . ucfirst($data['typeFilter'])]);
            }
            $csv->insertOne([]);
            $csv->insertOne(['Category Name', 'Type', 'Total Amount (BDT)', 'Transaction Count']);

            foreach ($data['categorySummaries'] as $summary) {
                $csv->insertOne([
                    $summary->category_name,
                    ucfirst($summary->category_type),
                    $summary->total_amount,
                    $summary->transaction_count
                ]);
            }
             // Optional: Grand Totals for CSV if not filtered by type
            if (!$data['typeFilter'] && $data['categorySummaries']->isNotEmpty()) {
                $grandTotalIncome = $data['categorySummaries']->where('category_type', 'income')->sum('total_amount');
                $grandTotalExpense = $data['categorySummaries']->where('category_type', 'expense')->sum('total_amount');
                $csv->insertOne([]);
                $csv->insertOne(['Grand Total Income', '', $grandTotalIncome, '']);
                $csv->insertOne(['Grand Total Expense', '', $grandTotalExpense, '']);
                $csv->insertOne(['Net', '', $grandTotalIncome - $grandTotalExpense, '']);
            }

            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        }
    }
}
