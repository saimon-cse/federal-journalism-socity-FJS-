<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\MembershipType;
use App\Models\PaymentAccount; // To show payment options
use App\Models\PaymentMethod;
// use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\MembershipApplied;
use App\Mail\MembershipPaymentPending;
use Carbon\Carbon;

class MembershipController extends Controller
{
    public function showApplyForm()
    {
        $user = Auth::user();
        $activeMembership = $user->activeMembership()->first();
        $pendingApplication = $user->latestMembershipApplication()->whereIn('status', ['pending_application', 'pending_payment'])->first();

        if ($activeMembership) {
            return redirect()->route('user.membership.status')->with('info', 'You already have an active membership.');
        }
        if ($pendingApplication) {
            if ($pendingApplication->status === 'pending_payment') {
                return redirect()->route('user.membership.payment.form', $pendingApplication);
            }
            return redirect()->route('user.membership.status')->with('info', 'You have a pending membership application.');
        }

         $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        if ($membershipTypes->isEmpty()) {
            return view('user.membership.no_types_available'); // Create this simple view
        }


        return view('user.membership.apply', compact('membershipTypes'));
    }

    public function processApplication(Request $request)
    {
        $user = Auth::user();
        if ($user->activeMembership()->exists() || $user->latestMembershipApplication()->whereIn('status', ['pending_application', 'pending_payment'])->exists()) {
            return redirect()->route('user.membership.status')->with('error', 'Action not allowed at this time.');
        }

        $request->validate([
            'membership_type_id' => 'required|exists:membership_types,id', // Validate against
            // Add NID, Photo if not in user profile and required here
            // 'nid_document' => 'required_if_not_profile_has_nid|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // 'profile_photo' => 'required_if_not_profile_has_photo|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Fetch membership types/options
         $selectedType = MembershipType::where('is_active', true)->find($request->membership_type_id);

        if (!$selectedType) {
            return redirect()->back()->with('error', 'Invalid membership type selected or type is not active.');
        }

        // Determine fee - user will choose monthly or annual on apply form if both exist
        // For simplicity now, let's assume user is applying for the 'default' fee (e.g., annual)
        // Or you add a 'payment_cycle' (monthly/annual) to the apply form.
        // Let's assume for now we use annual if available, else monthly.
        $fee = $selectedType->getFeeForCycle('annual'); // Or pass cycle from form
        if ($fee === null) { // If no fee is set for the type at all
             return redirect()->back()->with('error', 'Membership fee not configured for this type.');
        }


        $status = 'pending_payment';


       $membership = Membership::create([
            'user_id' => $user->id,
            'membership_type_id' => $selectedType->id, // Store the ID
            'status' => $status,
            // 'remarks' => 'User submitted application.',
        ]);

        // Send Application Received Email (if status was pending_application)
        // Mail::to($user->email)->send(new MembershipApplied($membership));

        // If fee is 0, activate directly (or set to pending_approval if admin must click)
        if ($fee <= 0) {
            // ... (Logic for free membership activation, as before, but use $selectedType->getDurationInMonths())
            $durationMonths = $selectedType->getDurationInMonths();
            $membership->update([
                'status' => 'active',
                'start_date' => Carbon::today(),
                'end_date' => $durationMonths ? Carbon::today()->addMonths($durationMonths)->subDay() : null,
                'approved_by_user_id' => null,
                'approved_at' => now(),
                'last_payment_date' => now(),
            ]);
            if (!$user->hasRole('Registered Member')) {
                $user->assignRole('Registered Member');
            }
            // Mail::to($user->email)->send(new MembershipActivated($membership)); // Ensure mailable uses membershipType relation
            return redirect()->route('user.membership.status')->with('success', 'Membership application submitted and activated (Free Tier)!');
        }

        // Redirect to payment form
                return redirect()->route('user.membership.payment.form', $membership);

    }

    public function showPaymentForm(Membership $membership)
    {
        $this->authorize('manage', $membership); // Policy for ownership

        if ($membership->status !== 'pending_payment') {
            return redirect()->route('user.membership.status')->with('info', 'Payment is not currently pending for this membership.');
        }

         $selectedType = $membership->membershipType; // Get related type
        if (!$selectedType) {
            return redirect()->route('user.membership.status')->with('error', 'Membership type configuration error.');
        }

        // Again, assume annual fee or add cycle selection
        $fee = $selectedType->getFeeForCycle('annual');
         if ($fee === null) {
             return redirect()->route('user.membership.status')->with('error', 'Membership fee not configured for this type.');
        }

            $paymentMethods = PaymentMethod::where('is_active', true)
            ->where('type', 'manual')
            ->whereNotNull('default_manual_account_id')
            ->with('defaultManualAccount')
            ->orderBy('sort_order')
            ->get();

        if ($paymentMethods->isEmpty()) {
             return view('user.membership.no_payment_methods', compact('membership', 'fee'));
        }

        return view('user.membership.payment', compact('membership', 'fee', 'paymentMethods'));    }

    public function processPayment(Request $request, Membership $membership)
    {
        $this->authorize('manage', $membership);

        if ($membership->status !== 'pending_payment') {
            return redirect()->route('user.membership.status')->with('error', 'Payment is not currently pending.');
        }

        $selectedType = $membership->membershipType;
        $fee = $selectedType->getFeeForCycle('annual'); // Or from selected cycle
         if ($fee === null) {
             return redirect()->route('user.membership.status')->with('error', 'Membership fee not configured for this type.');
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_id' => 'required|string|max:255',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
            'payment_datetime' => 'required|date',
        ]);

        $paymentMethod = PaymentMethod::find($request->payment_method_id);
        if (!$paymentMethod || $paymentMethod->type !== 'manual' || !$paymentMethod->default_manual_account_id) {
            return redirect()->back()->with('error', 'Invalid payment method selected.');
        }

        // Create a Payment record
        $payment = $membership->payments()->create([
            'payment_uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'amount_due' => $fee,
            'amount_paid' => $fee, // Assume user pays full amount for now
            'currency_code' => 'BDT',
            'net_amount_payable' => $fee,
            'payment_method_id' => $paymentMethod->id,
            'manual_transaction_id_user' => $request->transaction_id,
            'manual_payment_datetime_user' => Carbon::parse($request->payment_datetime),
            'manual_payment_to_account_id' => $paymentMethod->default_manual_account_id,
            'status' => 'pending_manual_verification',
            'notes' => 'Membership fee payment submitted by user.',
        ]);

        if ($request->hasFile('payment_proof')) {
            $media = $payment->addMediaFromRequest('payment_proof')->toMediaCollection('payment_proofs');
            // Optionally, store only the file name (without localhost or domain)
            $payment->update(['payment_proof_filename' => $media->file_name]);
        }

        // Update membership status or remarks if needed
        // $membership->update(['remarks' => 'Payment proof submitted. Awaiting verification.']);

        // Send Payment Pending/Submitted Email
        Mail::to(Auth::user()->email)->send(new MembershipPaymentPending($membership, $payment));
        // Optionally notify admin
        // Mail::to(config('mail.admin_address'))->send(new AdminMembershipPaymentSubmitted($membership, $payment));


        return redirect()->route('user.membership.status')->with('success', 'Payment details submitted successfully. Please wait for verification.');
    }


    public function showStatus()
    {
        $user = Auth::user();
        $memberships = $user->memberships()->with(['membershipType', 'payments.media'])->orderBy('created_at', 'desc')->paginate(5);
        $activeMembership = $user->activeMembership()->with('membershipType')->first();
        $latestApplication = $user->latestMembershipApplication()->with(['membershipType', 'payments.media'])->first();

        return view('user.membership.status', compact('user', 'memberships', 'activeMembership', 'latestApplication'));
   }
}
