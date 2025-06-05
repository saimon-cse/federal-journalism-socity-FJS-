<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedger;
use App\Models\FinancialTransactionCategory;
use App\Models\PaymentAccount;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For DB Transactions
use Illuminate\Support\Str; // For UUID

class FinancialLedgerController extends Controller
{
    public function index(Request $request)
    {
        // Use a broader permission for viewing the ledger or a specific one
        $this->authorize('view-financial-reports'); // Or 'view-financial-ledger'

        $query = FinancialLedger::with(['category', 'fromPaymentAccount', 'toPaymentAccount', 'recordedByUser', 'payment']);

        // Filters
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('payment_account_id')) {
            $accountId = $request->payment_account_id;
            $query->where(function ($q) use ($accountId) {
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
        if ($request->filled('search_description')) {
            $query->where('description', 'like', '%' . $request->search_description . '%');
        }


        $ledgers = $query->orderBy('transaction_datetime', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $entryTypes = ['income', 'expense', 'transfer', 'opening_balance', 'reconciliation_adjustment'];
        $categories = FinancialTransactionCategory::orderBy('name')->get();
        $paymentAccounts = PaymentAccount::orderBy('account_name')->get();


        return view('admin.financial_ledgers.index', compact('ledgers', 'entryTypes', 'categories', 'paymentAccounts'));
    }

    public function create()
    {
        // Check if user can perform at least one type of ledger entry
        $this->authorize('record-income'); // Or use a general 'create-ledger-entry' permission
                                          // And then check specific types in store method

        $incomeCategories = FinancialTransactionCategory::where('type', 'income')->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = FinancialTransactionCategory::where('type', 'expense')->where('is_active', true)->orderBy('name')->get();
        $paymentAccounts = PaymentAccount::where('is_active', true)->orderBy('account_name')->get();
        $paymentsWithoutLedger = Payment::where('status', 'successful') // Only successful payments
                                   ->whereDoesntHave('financialLedgerEntries') // That don't have a ledger entry yet
                                   ->select('id', 'payment_uuid', 'amount_paid', 'payable_type', 'payable_id')
                                   ->take(100) // Limit for dropdown performance
                                   ->get();
        return view('admin.financial_ledgers.create', compact('incomeCategories', 'expenseCategories', 'paymentAccounts', 'paymentsWithoutLedger'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_datetime' => 'required|date',
            'entry_type' => 'required|in:income,expense,transfer,opening_balance,reconciliation_adjustment',
            'amount' => 'required|numeric|min:0.01|max:9999999999.99', // Max based on decimal(12,2) or (15,2)
            'description' => 'required|string|max:1000',
            'category_id' => 'nullable|required_if:entry_type,income,expense|exists:financial_transaction_categories,id',
            'from_payment_account_id' => 'nullable|required_if:entry_type,expense,transfer|exists:payment_accounts,id',
            'to_payment_account_id' => 'nullable|required_if:entry_type,income,transfer,opening_balance|exists:payment_accounts,id',
            'external_party_name' => 'nullable|string|max:255',
            'external_reference_id' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string|max:1000',
            'payment_id_link' => 'nullable|exists:payments,id',
        ]);

        // Authorization check based on type
        if (in_array($validated['entry_type'], ['income', 'opening_balance'])) {
            $this->authorize('record-income');
        } elseif ($validated['entry_type'] === 'expense') {
            $this->authorize('record-expense');
        } elseif ($validated['entry_type'] === 'transfer') {
            $this->authorize('transfer-funds-between-accounts');
        } // Add more for reconciliation_adjustment if needed (e.g., 'reconcile-transactions')

        if ($validated['entry_type'] === 'transfer' && isset($validated['from_payment_account_id']) && isset($validated['to_payment_account_id']) && $validated['from_payment_account_id'] == $validated['to_payment_account_id']) {
            return redirect()->back()->withInput()->withErrors(['from_payment_account_id' => 'From and To accounts cannot be the same for a transfer.']);
        }

        DB::transaction(function () use ($validated) {
            $amount = (float) $validated['amount'];

            FinancialLedger::create([
                'ledger_entry_uuid' => (string) Str::uuid(),
                'transaction_datetime' => $validated['transaction_datetime'],
                'entry_type' => $validated['entry_type'],
                'amount' => $amount,
                'payment_id' => $validated['payment_id_link'] ?? null, // ADDED: Link if provided
                'referenceable_id' => $validated['referenceable_id'] ?? ($validated['payment_id_link'] ? Payment::find($validated['payment_id_link'])->payable_id : null),
            'referenceable_type' => $validated['referenceable_type'] ?? ($validated['payment_id_link'] ? Payment::find($validated['payment_id_link'])->payable_type : null),
                'description' => $validated['description'],
                'category_id' => ($validated['entry_type'] === 'income' || $validated['entry_type'] === 'expense') ? $validated['category_id'] : null,
                'from_payment_account_id' => ($validated['entry_type'] === 'expense' || $validated['entry_type'] === 'transfer') ? $validated['from_payment_account_id'] : null,
                'to_payment_account_id' => (in_array($validated['entry_type'], ['income', 'transfer', 'opening_balance'])) ? $validated['to_payment_account_id'] : null,
                // Note: 'reconciliation_adjustment' might affect 'from' or 'to' based on its nature (e.g. bank charge is expense-like)
                'external_party_name' => $validated['external_party_name'] ?? null,
                'external_reference_id' => $validated['external_reference_id'] ?? null,
                'recorded_by_user_id' => Auth::id(),
                'internal_notes' => $validated['internal_notes'] ?? null,
                'currency_code' => 'BDT', // Or from a setting
            ]);

            // Update Payment Account Balances
            if ($validated['entry_type'] === 'income' || $validated['entry_type'] === 'opening_balance') {
                if (isset($validated['to_payment_account_id'])) {
                    $accountTo = PaymentAccount::find($validated['to_payment_account_id']);
                    if ($accountTo) {
                        $accountTo->increment('current_balance', $amount);
                    }
                }
            } elseif ($validated['entry_type'] === 'expense') {
                if (isset($validated['from_payment_account_id'])) {
                    $accountFrom = PaymentAccount::find($validated['from_payment_account_id']);
                    if ($accountFrom) {
                        $accountFrom->decrement('current_balance', $amount);
                    }
                }
            } elseif ($validated['entry_type'] === 'transfer') {
                if (isset($validated['from_payment_account_id'])) {
                    $accountFrom = PaymentAccount::find($validated['from_payment_account_id']);
                    if ($accountFrom) {
                        $accountFrom->decrement('current_balance', $amount);
                    }
                }
                if (isset($validated['to_payment_account_id'])) {
                    $accountTo = PaymentAccount::find($validated['to_payment_account_id']);
                    if ($accountTo) {
                        $accountTo->increment('current_balance', $amount);
                    }
                }
            } elseif ($validated['entry_type'] === 'reconciliation_adjustment') {
                // This needs more logic: is it an income-like adjustment or expense-like?
                // And which account does it affect?
                // Example: if a bank charge, it's like an expense.
                // if (isset($validated['from_payment_account_id'])) { // Assuming 'from' for bank charge
                //     $accountAdjust = PaymentAccount::find($validated['from_payment_account_id']);
                //     if ($accountAdjust) $accountAdjust->decrement('current_balance', $amount);
                // } else if (isset($validated['to_payment_account_id'])) { // Assuming 'to' for interest earned
                //      $accountAdjust = PaymentAccount::find($validated['to_payment_account_id']);
                //     if ($accountAdjust) $accountAdjust->increment('current_balance', $amount);
                // }
                // For now, reconciliation adjustments need manual balance update or a clearer UI indication.
            }
        });

        return redirect()->route('admin.financial-ledgers.index')->with('success', 'Ledger entry recorded successfully.');
    }

    /**
     * Display the specified resource.
     * (Optional: for viewing a single ledger entry in detail, if needed)
     */
    // public function show(FinancialLedger $financialLedger)
    // {
    //     $this->authorize('view-financial-reports');
    //     $financialLedger->load(['category', 'fromPaymentAccount', 'toPaymentAccount', 'recordedByUser', 'payment']);
    //     return view('admin.financial_ledgers.show', compact('financialLedger'));
    // }


    // Edit, Update, Destroy for ledger entries are typically complex and might be disallowed
    // or require very strict auditing and reversal logic.
    // If implementing, ensure balance adjustments are correctly reversed and reapplied.
}
