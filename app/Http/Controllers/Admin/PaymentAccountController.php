<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-payment-accounts'); // Or more granular if needed
    }

    public function index(): View
    {
        $paymentAccounts = PaymentAccount::latest()->paginate(10);
        return view('admin.payment_accounts.index', compact('paymentAccounts'));
    }

    public function create(): View
    {
        $accountTypes = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'bank_account' => 'Bank Account', 'other' => 'Other'];
        return view('admin.payment_accounts.create', compact('accountTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:bkash,nagad,rocket,bank_account,other',
            'account_identifier' => 'required|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255|required_if:account_type,bank_account',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'instructions_for_payer' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        PaymentAccount::create($validated);
        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment account created.');
    }

    public function edit(PaymentAccount $paymentAccount): View
    {
        $accountTypes = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'bank_account' => 'Bank Account', 'other' => 'Other'];
        return view('admin.payment_accounts.edit', compact('paymentAccount', 'accountTypes'));
    }

    public function update(Request $request, PaymentAccount $paymentAccount): RedirectResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:bkash,nagad,rocket,bank_account,other',
            'account_identifier' => 'required|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255|required_if:account_type,bank_account',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'instructions_for_payer' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $paymentAccount->update($validated);
        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment account updated.');
    }

    public function destroy(PaymentAccount $paymentAccount): RedirectResponse
    {
        // Add check if account is in use by any pending payments or configurations
        if ($paymentAccount->paymentsReceived()->whereIn('status', ['pending_verification'])->count() > 0) {
             return redirect()->route('admin.payment-accounts.index')->with('error', 'Cannot delete account. It has pending payments associated with it.');
        }
        $paymentAccount->delete();
        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment account deleted.');
    }
}
