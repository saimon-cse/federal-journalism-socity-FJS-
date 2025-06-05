<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentAccount; // ADDED
use App\Models\FinancialLedger; // ADDED
use App\Models\FinancialTransactionCategory; // ADDED
use App\Events\PaymentVerified; // Ensure this is the correct namespace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ADDED
use Illuminate\Support\Str;      // ADDED
use Illuminate\Support\Facades\Log; // For logging

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-all-payments'); // Or more specific for verification queue

        $query = Payment::with(['user', 'payable', 'paymentMethod', 'manualPaymentToAccount', 'verifiedBy']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payable_type')) {
            // You might need to map user-friendly names to model class names
            // For now, assuming direct model class name
            if (class_exists('App\\Models\\' . $request->payable_type)) {
                $query->where('payable_type', 'App\\Models\\' . $request->payable_type);
            }
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('search_term')) {
            $term = $request->search_term;
            $query->where(function ($q) use ($term) {
                $q->where('payment_uuid', 'like', "%{$term}%")
                    ->orWhere('manual_transaction_id_user', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
            });
        }


        $payments = $query->latest()->paginate(15)->withQueryString();
        $paymentStatuses = ['pending_manual_verification', 'successful', 'failed_verification', 'pending_user_action', 'processing_gateway', 'failed_gateway', 'expired', 'cancelled', 'refunded']; // Add all relevant statuses
        $payableTypes = ['Membership', 'TrainingRegistration', 'EventRegistration', 'ElectionNomination']; // Example user-friendly names

        return view('admin.payments.index', compact('payments', 'paymentStatuses', 'payableTypes'));
    }

    public function show(Payment $payment)
    {
        $this->authorize('view-all-payments'); // Or specific view permission
        $payment->load(['user', 'payable', 'paymentMethod', 'manualPaymentToAccount', 'verifiedBy', 'media']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $this->authorize('verify-manual-payments');

        $request->validate([
            'verification_remarks' => 'nullable|string|max:1000',
        ]);

        if ($payment->status !== 'pending_manual_verification') {
            return redirect()->back()->with('error', 'This payment is not pending verification or has already been processed.');
        }

        $targetPaymentAccount = $payment->manualPaymentToAccount;

        if (!$targetPaymentAccount) {
            Log::error("Payment verification failed: No target payment account found for payment UUID {$payment->payment_uuid}.");
            return redirect()->back()->with('error', 'Verification failed: Target payment account not specified for this payment.');
        }

        try {
            DB::transaction(function () use ($payment, $request, $targetPaymentAccount) {
                $payment->update([
                    'status' => 'successful',
                    'verified_by_user_id' => Auth::id(),
                    'verified_at' => now(),
                    'verification_remarks' => $request->verification_remarks,
                    'amount_paid' => $payment->amount_due // Confirm amount paid matches amount due
                ]);

                $categoryName = 'General Income';
                $payableTypeBase = class_basename($payment->payable_type); // e.g., "Membership"

                // --- START FIX ---
                $referenceableType = $payment->payable_type; // e.g., App\Models\Membership
                $referenceableId = $payment->payable_id;   // e.g., 2
                // --- END FIX ---

                if ($payableTypeBase === 'Membership') {
                    $categoryName = 'Membership Fee Income';
                } elseif ($payableTypeBase === 'TrainingRegistration') {
                    $categoryName = 'Training Fee Income';
                } elseif ($payableTypeBase === 'EventRegistration') {
                    $categoryName = 'Event Fee Income';
                } // Add more mappings as needed

                $incomeCategory = FinancialTransactionCategory::firstOrCreate(
                    ['name' => $categoryName, 'type' => 'income'],
                    ['description' => ucfirst($categoryName) . " via system payment.", 'is_active' => true] // Added more context
                );

                FinancialLedger::create([
                    'ledger_entry_uuid' => (string) Str::uuid(),
                    'transaction_datetime' => $payment->verified_at,
                    'entry_type' => 'income',
                    'amount' => $payment->amount_paid,
                    'currency_code' => $payment->currency_code,
                    'description' => "Payment received for {$payableTypeBase} #{$referenceableId}. User TrxID: {$payment->manual_transaction_id_user}. Payment UUID: {$payment->payment_uuid}",
                    'category_id' => $incomeCategory->id,
                    'to_payment_account_id' => $targetPaymentAccount->id,
                    'payment_id' => $payment->id,
                    // --- START FIX ---
                    'referenceable_id' => $referenceableId,
                    'referenceable_type' => $referenceableType,
                    // --- END FIX ---
                    'recorded_by_user_id' => Auth::id(),
                ]);

                $targetPaymentAccount->increment('current_balance', $payment->amount_paid);

                event(new PaymentVerified($payment));
            });

            return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment verified successfully and ledger updated.');
        } catch (\Exception $e) {
            Log::error("Payment verification transaction failed for payment UUID {$payment->payment_uuid}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An error occurred during payment verification. Please check logs for details.' . '' . $e->getMessage());
        }
    }


    public function reject(Request $request, Payment $payment)
    {
        $this->authorize('verify-manual-payments'); // Same permission for rejection

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($payment->status !== 'pending_manual_verification') {
            return redirect()->back()->with('error', 'This payment is not pending verification.');
        }

        $payment->update([
            'status' => 'failed_verification',
            'verified_by_user_id' => Auth::id(), // User who rejected
            'verified_at' => now(), // Timestamp of rejection
            'verification_remarks' => $request->rejection_reason, // Store reason
        ]);

        // Notify user about rejection if needed
        // event(new PaymentVerificationFailed($payment, $request->rejection_reason));

        return redirect()->route('admin.payments.show', $payment)->with('warning', 'Payment verification rejected.');
    }
}
