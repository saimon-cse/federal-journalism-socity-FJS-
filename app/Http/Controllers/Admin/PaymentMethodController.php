<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentAccount; // To list accounts for manual methods
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-payment-method-settings');
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::with('defaultManualAccount')->orderBy('sort_order')->paginate(10);
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        $manualPaymentAccounts = PaymentAccount::where('is_active', true)
                                               ->where('allow_user_manual_payment_to', true)
                                               ->get();
        return view('admin.payment_methods.create', compact('manualPaymentAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method_key' => 'required|string|max:100|unique:payment_methods,method_key|regex:/^[a-z0-9_]+$/', // e.g., bkash_manual
            'type' => 'required|in:manual,gateway',
            'provider_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255', // Or file upload
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
            'default_manual_account_id' => 'nullable|required_if:type,manual|exists:payment_accounts,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        if ($request->type === 'gateway') {
            $data['default_manual_account_id'] = null;
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $manualPaymentAccounts = PaymentAccount::where('is_active', true)
                                               ->where('allow_user_manual_payment_to', true)
                                               ->get();
        return view('admin.payment_methods.edit', compact('paymentMethod', 'manualPaymentAccounts'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method_key' => 'required|string|max:100|unique:payment_methods,method_key,' . $paymentMethod->id . '|regex:/^[a-z0-9_]+$/',
            'type' => 'required|in:manual,gateway',
            'provider_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
            'default_manual_account_id' => 'nullable|required_if:type,manual|exists:payment_accounts,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        if ($request->type === 'gateway') {
            $data['default_manual_account_id'] = null;
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Add checks if method is in use
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method deleted successfully.');
    }
}
