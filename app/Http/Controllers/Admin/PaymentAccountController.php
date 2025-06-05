<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For transactions

class PaymentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-payment-accounts');
    }

    public function index()
    {
        $paymentAccounts = PaymentAccount::latest()->paginate(10);
        return view('admin.payment_accounts.index', compact('paymentAccounts'));
    }

    public function create()
    {
        return view('admin.payment_accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255|unique:payment_accounts,account_name',
            'account_provider' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'account_identifier' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0', // ADDED
            'is_active' => 'sometimes|boolean',
            'allow_user_manual_payment_to' => 'sometimes|boolean',
            'manual_payment_instructions' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['allow_user_manual_payment_to'] = $request->has('allow_user_manual_payment_to');
        $data['current_balance'] = (float) $request->initial_balance; // Set current balance to initial on creation

        PaymentAccount::create($data);

        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account created successfully.');
    }

    public function edit(PaymentAccount $paymentAccount)
    {
        return view('admin.payment_accounts.edit', compact('paymentAccount'));
    }

public function update(Request $request, PaymentAccount $paymentAccount)
{
    $isUsed = $paymentAccount->financialLedgersFrom()->exists() ||
                $paymentAccount->financialLedgersTo()->exists();

    $rules = [
        'account_name' => 'required|string|max:255|unique:payment_accounts,account_name,' . $paymentAccount->id,
        'account_provider' => 'required|string|max:255',
        'account_type' => 'required|string|max:255',
        'account_identifier' => 'nullable|string|max:255',
        // ... other fields ...
        'is_active' => 'sometimes|boolean',
        'allow_user_manual_payment_to' => 'sometimes|boolean',
        'manual_payment_instructions' => 'nullable|string',


            // 'account_holder_name' => 'nullable|string|max:255',
            // 'bank_name' => 'nullable|string|max:255',
            // 'branch_name' => 'nullable|string|max:255',
            // 'routing_number' => 'nullable|string|max:255',
            // 'initial_balance' => 'required|numeric|min:0', // Allow editing, with caution

    ];

    if (!$isUsed) { // Only allow initial_balance edit if account hasn't been used in transactions
        $rules['initial_balance'] = 'required|numeric|min:0';
    } else {
        // If used, maybe show initial_balance as readonly or don't include it in validation/update
         $request->request->remove('initial_balance'); // Ensure it's not updated if present in form
    }

    $request->validate($rules);

    DB::transaction(function () use ($request, $paymentAccount, $isUsed) {
        $data = $request->except(['initial_balance']); // Exclude initial_balance by default
        $data['is_active'] = $request->has('is_active');
        $data['allow_user_manual_payment_to'] = $request->has('allow_user_manual_payment_to');

        if (!$isUsed && $request->has('initial_balance')) {
            $newInitialBalance = (float) $request->initial_balance;
            // If initial balance changes AND no transactions, update current balance too
            if ($newInitialBalance !== (float) $paymentAccount->initial_balance) {
                $data['initial_balance'] = $newInitialBalance;
                $data['current_balance'] = $newInitialBalance; // Reset current balance to new initial
            }
        }
        // DO NOT allow $data['current_balance'] = $request->current_balance;

        $paymentAccount->update($data);
    });

    return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account updated successfully.');
}

    public function destroy(PaymentAccount $paymentAccount)
    {
        if ($paymentAccount->financialLedgersFrom()->exists() || $paymentAccount->financialLedgersTo()->exists() || $paymentAccount->paymentsToThisAccount()->exists()) {
             return redirect()->route('admin.payment-accounts.index')->with('error', 'Cannot delete payment account. It has associated financial transactions or payments.');
        }
        // Further checks for usage in payment methods, trainings, elections etc.

        try {
            $paymentAccount->delete();
            return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.payment-accounts.index')->with('error', 'Cannot delete payment account. It might be in use.');
        }
    }
}
