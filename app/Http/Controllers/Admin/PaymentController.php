<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $query->where(function($q) use ($term) {
                $q->where('payment_uuid', 'like', "%{$term}%")
                  ->orWhere('manual_transaction_id_user', 'like', "%{$term}%")
                  ->orWhereHas('user', function($uq) use ($term){
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
            return redirect()->back()->with('error', 'This payment is not pending verification.');
        }

        // Logic to actually verify (e.g., checking bank statement - this is an admin action)
        // For now, we assume the admin has verified it externally.

        $payment->update([
            'status' => 'successful',
            'verified_by_user_id' => Auth::id(),
            'verified_at' => now(),
            'verification_remarks' => $request->verification_remarks,
        ]);

        // Here, you would typically trigger an event to activate the 'payable'
        // e.g., ActivateMembershipJob::dispatch($payment->payable);
        //      EnrollInTrainingJob::dispatch($payment->payable);
        // For now, just a success message.
        // event(new PaymentVerified($payment));


        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment verified successfully.');
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
