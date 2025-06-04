<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedger;
use App\Models\FinancialTransactionCategory;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // For UUID

class FinancialLedgerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-financial-reports'); // Or a specific ledger view permission

        $query = FinancialLedger::with(['category', 'fromPaymentAccount', 'toPaymentAccount', 'recordedByUser', 'payment']);

        // Add filters as needed (date range, type, account, category)
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('payment_account_id')) {
            $accountId = $request->payment_account_id;
            $query->where(function($q) use ($accountId){
                $q->where('from_payment_account_id', $accountId)
                  ->orWhere('to_payment_account_id', $accountId);
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_datetime', '<=', $request->end_date);
        }


        $ledgers = $query->orderBy('transaction_datetime', 'desc')->paginate(20)->withQueryString();
        $entryTypes = ['income', 'expense', 'transfer', 'opening_balance', 'reconciliation_adjustment'];
        $categories = FinancialTransactionCategory::orderBy('name')->get();
        $paymentAccounts = PaymentAccount::orderBy('account_name')->get();


        return view('admin.financial_ledgers.index', compact('ledgers', 'entryTypes', 'categories', 'paymentAccounts'));
    }

    public function create()
    {
        $this->authorize('record-income'); // Or 'record-expense', 'transfer-funds-between-accounts'
        $incomeCategories = FinancialTransactionCategory::where('type', 'income')->orderBy('name')->get();
        $expenseCategories = FinancialTransactionCategory::where('type', 'expense')->orderBy('name')->get();
        $paymentAccounts = PaymentAccount::where('is_active', true)->orderBy('account_name')->get();
        return view('admin.financial_ledgers.create', compact('incomeCategories', 'expenseCategories', 'paymentAccounts'));
    }

    public function store(Request $request)
    {
        // Depending on entry_type, some fields become required/optional
        $validated = $request->validate([
            'transaction_datetime' => 'required|date',
            'entry_type' => 'required|in:income,expense,transfer,opening_balance,reconciliation_adjustment',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'category_id' => 'nullable|required_if:entry_type,income,expense|exists:financial_transaction_categories,id',
            'from_payment_account_id' => 'nullable|required_if:entry_type,expense,transfer|exists:payment_accounts,id',
            'to_payment_account_id' => 'nullable|required_if:entry_type,income,transfer,opening_balance|exists:payment_accounts,id',
            'external_party_name' => 'nullable|string|max:255',
            'external_reference_id' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string',
        ]);

        // Authorization check based on type
        if (in_array($validated['entry_type'], ['income', 'opening_balance'])) {
            $this->authorize('record-income');
        } elseif ($validated['entry_type'] === 'expense') {
            $this->authorize('record-expense');
        } elseif ($validated['entry_type'] === 'transfer') {
            $this->authorize('transfer-funds-between-accounts');
        } // Add more for reconciliation_adjustment if needed

        if ($validated['entry_type'] === 'transfer' && $validated['from_payment_account_id'] == $validated['to_payment_account_id']) {
            return redirect()->back()->withInput()->withErrors(['from_payment_account_id' => 'From and To accounts cannot be the same for a transfer.']);
        }

        FinancialLedger::create([
            'ledger_entry_uuid' => (string) Str::uuid(),
            'transaction_datetime' => $validated['transaction_datetime'],
            'entry_type' => $validated['entry_type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category_id' => $validated['entry_type'] === 'income' || $validated['entry_type'] === 'expense' ? $validated['category_id'] : null,
            'from_payment_account_id' => $validated['entry_type'] === 'expense' || $validated['entry_type'] === 'transfer' ? $validated['from_payment_account_id'] : null,
            'to_payment_account_id' => in_array($validated['entry_type'], ['income', 'transfer', 'opening_balance']) ? $validated['to_payment_account_id'] : null,
            'external_party_name' => $validated['external_party_name'] ?? null,
            'external_reference_id' => $validated['external_reference_id'] ?? null,
            'recorded_by_user_id' => Auth::id(),
            'internal_notes' => $validated['internal_notes'] ?? null,
            'currency_code' => 'BDT', // Or make this configurable
        ]);

        return redirect()->route('admin.financial-ledgers.index')->with('success', 'Ledger entry recorded successfully.');
    }

    // Add edit, update, destroy for ledger if allowed, with careful consideration of accounting principles.
    // Deleting/editing financial records is often restricted.
}
