<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;

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
            'account_provider' => 'required|string|max:255', // e.g., Bkash, Nagad, DBBL
            'account_type' => 'required|string|max:255', // e.g., Mobile Financial Service, Bank Account
            'account_identifier' => 'nullable|string|max:255', // Number or Account No.
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'allow_user_manual_payment_to' => 'sometimes|boolean',
            'manual_payment_instructions' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['allow_user_manual_payment_to'] = $request->has('allow_user_manual_payment_to');

        PaymentAccount::create($data);

        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account created successfully.');
    }

    public function edit(PaymentAccount $paymentAccount)
    {
        return view('admin.payment_accounts.edit', compact('paymentAccount'));
    }

    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $request->validate([
            'account_name' => 'required|string|max:255|unique:payment_accounts,account_name,' . $paymentAccount->id,
            'account_provider' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'account_identifier' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'allow_user_manual_payment_to' => 'sometimes|boolean',
            'manual_payment_instructions' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['allow_user_manual_payment_to'] = $request->has('allow_user_manual_payment_to');

        $paymentAccount->update($data);

        return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account updated successfully.');
    }

    public function destroy(PaymentAccount $paymentAccount)
    {
        // Add checks here if this account is in use (e.g., in payments, trainings)
        // For simplicity, direct delete for now.
        try {
            $paymentAccount->delete();
            return redirect()->route('admin.payment-accounts.index')->with('success', 'Payment Account deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch foreign key constraint violation
            return redirect()->route('admin.payment-accounts.index')->with('error', 'Cannot delete payment account. It might be in use.');
        }
    }
}
