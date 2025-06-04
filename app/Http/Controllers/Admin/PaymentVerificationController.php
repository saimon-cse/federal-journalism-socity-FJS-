<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User; // For payable type and verifier
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

//spartie/permission/models/Role is used for assigning roles
use Spatie\Permission\Models\Role; // For assigning Member role
use Illuminate\Support\Facades\Notification; // If sending notifications
// use App\Notifications\MembershipApprovedNotification; // Example
// use App\Notifications\MembershipRejectedNotification; // Example

class PaymentVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-payments|verify-payments', ['only' => ['index', 'review']]);
        $this->middleware('permission:verify-payments', ['only' => ['verify', 'reject']]);
    }

    public function index(Request $request): View
    {
        $query = Payment::with(['payer:id,name,email', 'payable', 'paymentAccountReceivedIn:id,account_name,account_identifier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending_verification'); // Default
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('external_transaction_id', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('payer', function($userQuery) use ($search){
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();
        $statuses = ['pending_verification' => 'Pending Verification', 'verified' => 'Verified', 'rejected' => 'Rejected', 'refunded' => 'Refunded'];

        return view('admin.payments.index', compact('payments', 'statuses'));
    }

    public function review(Payment $payment): View
    {
        $payment->load(['payer.profile', 'payable', 'paymentAccountReceivedIn', 'verifier:id,name']);
        return view('admin.payments.review', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending_verification') {
            return redirect()->route('admin.payments.review', $payment)->with('error', 'This payment is not pending verification.');
        }

        $payment->status = 'verified';
        $payment->verified_by_user_id = Auth::id();
        $payment->verified_at = now();
        $payment->verification_notes = $request->input('verification_notes', 'Payment verified by admin.');
        $payment->save();

        // Trigger actions based on payable type
        $this->afterPaymentVerified($payment);

        return redirect()->route('admin.payments.index')->with('success', 'Payment verified successfully.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
         if ($payment->status !== 'pending_verification') {
            return redirect()->route('admin.payments.review', $payment)->with('error', 'This payment is not pending verification.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        $payment->status = 'rejected';
        $payment->verified_by_user_id = Auth::id();
        $payment->verified_at = now(); // Rejection is a form of "verification" action
        $payment->verification_notes = $request->rejection_reason;
        $payment->save();

        // Trigger actions based on payable type
        $this->afterPaymentRejected($payment);


        return redirect()->route('admin.payments.index')->with('success', 'Payment rejected.');
    }

    /**
     * Actions to take after a payment is successfully verified.
     * This is where you link payment verification to module-specific logic.
     */
    // protected function afterPaymentVerified(Payment $payment)
    // {
    //     // Example: Membership Application Fee
    //     if ($payment->payable_type === User::class && $payment->purpose === 'Membership Registration Fee') {
    //         $user = $payment->payable; // The user who applied
    //         if ($user && $user->membership_application_status === 'pending_approval') { // Or another status like 'awaiting_payment_verification'
    //             // This logic is now primarily in MembershipApplicationAdminController@approve
    //             // Here, you might just update user's application status if it was purely payment dependent before approval step.
    //             // For now, the main approval logic is in MembershipApplicationAdminController.
    //             // This function could send a notification or log an event specific to payment.
    //             // For example:
    //             // $user->membership_application_status = 'payment_verified_pending_final_approval';
    //             // $user->save();
    //         }
    //     }

    //     // Example: Training Registration Fee
    //     // if ($payment->payable_type === \App\Models\TrainingRegistration::class) {
    //     //     $registration = $payment->payable;
    //     //     $registration->status = 'payment_confirmed'; // Or 'enrolled'
    //     //     $registration->save();
    //     //     // Send enrollment confirmation email
    //     // }

    //     // Add more cases for other payable types (Events, Nominations, etc.)
    // }

    //  /**
    //  * Actions to take after a payment is rejected.
    //  */
    // protected function afterPaymentRejected(Payment $payment)
    // {
    //     if ($payment->payable_type === User::class && $payment->purpose === 'Membership Registration Fee') {
    //         $user = $payment->payable;
    //         if ($user && $user->membership_application_status === 'pending_approval') {
    //             // Update application status if payment rejection means application rejection
    //             // This logic is now primarily in MembershipApplicationAdminController@reject
    //             // $user->membership_application_status = 'payment_rejected';
    //             // $user->membership_rejection_reason = 'Payment verification failed: ' . $payment->verification_notes;
    //             // $user->save();
    //         }
    //     }
    //     // Handle other payable types if needed
    // }

    // ... in PaymentVerificationController ...
    protected function afterPaymentVerified(Payment $payment)
    {
        if ($payment->payable_type === User::class && $payment->purpose === 'Membership Registration Fee') {
            $user = $payment->payable; // This is the User model instance
            if ($user && $user->membership_application_status === 'pending_approval' && !$user->is_member) {
                $user->is_member = true;
                $user->membership_application_status = 'approved'; // Or 'active'
                $user->membership_start_date = now();
                // $user->membership_expires_on = now()->addYear(); // If annual
                $user->save();

                $memberRole = Role::where('name', 'Member')->first();
                if ($memberRole) {
                    $user->assignRole($memberRole);
                }

                // Send notification to user about membership approval
                // Notification::send($user, new MembershipApprovedNotification());

                return back()->with('success', "Membership for {$user->name} approved successfully.");
            }

        }
        // ... other payable types
    }

    protected function afterPaymentRejected(Payment $payment)
    {
        if ($payment->payable_type === User::class && $payment->purpose === 'Membership Registration Fee') {
            $user = $payment->payable;
            if ($user && $user->membership_application_status === 'pending_approval') {
                $user->membership_application_status = 'rejected'; // Or 'payment_rejected'
                $user->membership_rejection_reason = 'Payment verification failed: ' . $payment->verification_notes;
                $user->save();
                 // Send notification to user about membership rejection due to payment
                // Notification::send($user, new MembershipRejectedNotification('Payment Issue: '. $payment->verification_notes));
            }
        }
        // ... other payable types
    }
}
