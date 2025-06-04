<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Setting; // For membership fee
use App\Models\PaymentAccount; // To display payment options
use App\Models\Payment; // To record payment
use Illuminate\Support\Facades\Storage;

class MembershipController extends Controller
{
    public function createApplicationForm()
    {
        $user = Auth::user();
        // Check if user has already submitted essential profile info (NID, etc.)
        // This depends on your UserProfile setup.
        // if (empty($user->profile->nid_number) || empty($user->profile->father_name)) { // Example check
        //      return redirect()->route('frontend.profile.show') // Redirect to their main profile page
        //         ->with('warning', 'Please complete your basic profile information, including NID and father\'s name, before applying for membership.');
        // }

        $membershipFee = Setting::get('membership_registration_fee', 500); // Default 500 BDT
        $paymentAccounts = PaymentAccount::where('is_active', true)->get();

        return view('frontend.membership.apply', compact('user', 'membershipFee', 'paymentAccounts'));
    }

    public function storeApplication(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Double check if already applied or member
        if ($user->is_member || in_array($user->membership_application_status, ['pending_approval', 'pending_payment'])) {
            return redirect()->route('frontend.membership.application.status')->with('info', 'You have already applied or are a member.');
        }

        $request->validate([
            'membership_fee_paid' => 'required|numeric|min:'.Setting::get('membership_registration_fee', 1), // Ensure it's at least the fee
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'transaction_id' => 'required|string|max:100|unique:payments,external_transaction_id',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // 2MB Max for proof
            'agreed_to_terms' => 'required|accepted',
            // Add validation for any other application-specific fields you collect here
            // For example, if NID wasn't mandatory before but is for membership:
            // 'nid_file_application' => 'required_without:user.profile.nid_path|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // --- Handle NID upload if it's specific to this application ---
        // This assumes NID might be re-uploaded or uploaded for the first time here
        // Ideally, user profile should already have NID.
        // if ($request->hasFile('nid_file_application')) {
        //     if ($user->profile && $user->profile->nid_path && Storage::disk('public')->exists($user->profile->nid_path)) {
        //         // Optionally, don't delete old one if this is just an application specific copy
        //     }
        //     $nidPath = $request->file('nid_file_application')->store('membership_applications/nids', 'public');
        //     // You might store this path on a MembershipApplication model if you had one,
        //     // or just use it for the payment record's context if needed.
        // }


        // Store Payment Proof
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs/membership', 'public');

        // Create Payment Record
        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_id' => $user->id, // User themselves are the 'payable' entity for membership fee
            'payable_type' => User::class,
            'payment_account_id_received_in' => $request->payment_account_id,
            'amount' => $request->membership_fee_paid,
            'external_transaction_id' => $request->transaction_id,
            'payment_proof_path' => $paymentProofPath,
            'payment_date' => now(),
            'status' => 'pending_verification', // Default status
            'purpose' => 'Membership Registration Fee' // Add a purpose field to payments table if you don't have it
        ]);

        // Update user status
        $user->membership_application_status = 'pending_approval'; // Or 'pending_payment_verification'
        $user->save();

        // Send notification to admin (to be implemented later)
        // AdminNotification::sendNewMembershipApplication($user, $payment);

        return redirect()->route('frontend.membership.application.status')->with('success', 'Membership application submitted successfully! Your payment is pending verification.');
    }

    public function applicationStatus(): View
    {
        $user = Auth::user();
        return view('frontend.membership.status', compact('user'));
    }
}
