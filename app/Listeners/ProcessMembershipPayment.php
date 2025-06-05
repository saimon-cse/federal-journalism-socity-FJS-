<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Models\Membership;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipActivated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // For logging


class ProcessMembershipPayment implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentVerified $event): void
    {
        $payment = $event->payment;

        // Ensure we only process payments for Memberships and if the payment is successful
        if (!($payment->payable_type === Membership::class && $payment->status === 'successful')) {
            return;
        }

        /** @var Membership $membership */
        $membership = $payment->payable;

        // Check if the membership instance exists and is in a state that expects payment processing
        if (!$membership || !in_array($membership->status, ['pending_payment', 'pending_application'])) {
            // If status is already 'active', maybe it was manually activated. Log or decide action.
            // If it was 'pending_application', this verification implies application approval.
            Log::info("ProcessMembershipPayment: Membership ID {$membership->id} not in expected state (current: {$membership->status}) or not found for payment {$payment->id}.");
            return;
        }

        $membershipType = $membership->membershipType;
        if (!$membershipType) {
            Log::error("ProcessMembershipPayment: MembershipType not found for Membership ID {$membership->id}. Cannot activate.");
            // Potentially update membership status to an error state or notify admin
            $membership->update(['status' => 'configuration_error', 'remarks' => 'Membership type missing. Activation failed.']);
            return;
        }

        $startDate = $membership->start_date ?? Carbon::today(); // Use existing start_date if admin set it, else today
        $durationMonths = $membershipType->getDurationInMonths();
        $endDate = null;

        if ($durationMonths) {
            $endDate = Carbon::parse($startDate)->addMonths($durationMonths)->subDay();
        } elseif (strtolower($membershipType->membership_duration) !== 'lifetime') {
             // If duration is not explicitly lifetime and no months found, it's an issue or free short term.
             // For safety, let's assume if durationMonths is null and not 'lifetime', it defaults to a year or a configurable period.
             // Or, strictly, if duration is not set, make it non-expiring or flag an error.
             // For now, if duration is null AND not 'lifetime', we will leave end_date null (effectively lifetime)
             // This should ideally be clarified by membership_type.membership_duration value
            if(strtolower($membershipType->membership_duration) !== 'lifetime') {
                 Log::warning("MembershipType ID {$membershipType->id} has no parsable duration and is not 'lifetime'. Defaulting to non-expiring for Membership ID {$membership->id}.");
            }
        }


        $membership->update([
            'status' => 'active',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'last_payment_date' => $payment->verified_at ?? now(), // Use payment verification time
            'next_due_date' => ($endDate && $membershipType->is_recurring) ? $endDate->copy()->addDay() : null,
            'approved_by_user_id' => $payment->verified_by_user_id, // User who verified payment
            'approved_at' => $payment->verified_at ?? now(),
            'remarks' => $membership->remarks . ($membership->status === 'pending_application' ? ' Application approved via payment. ' : '') . 'Payment verified.',
        ]);

        if (!$membership->user->hasRole('Registered Member')) {
            $membership->user->assignRole('Registered Member');
        }

        try {
            Mail::to($membership->user->email)->send(new MembershipActivated($membership));
        } catch (\Exception $e) {
            Log::error("Failed to send membership activation email to {$membership->user->email} for membership ID {$membership->id}: " . $e->getMessage());
        }
    }
}
