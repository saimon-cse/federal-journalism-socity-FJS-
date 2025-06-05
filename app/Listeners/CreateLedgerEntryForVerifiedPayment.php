<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Models\FinancialLedger;
use App\Models\PaymentAccount;
use App\Models\FinancialTransactionCategory; // Important
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // For logging

class CreateLedgerEntryForVerifiedPayment implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentVerified $event): void
    {
        $payment = $event->payment;

        // Only proceed if payment is successful and has a target account
        if ($payment->status !== 'successful' || !$payment->manual_payment_to_account_id) {
            // Or if it's a gateway payment, find the account it was settled to
            // For now, focusing on manual_payment_to_account_id
            return;
        }

        DB::transaction(function () use ($payment) {
            $targetAccount = PaymentAccount::find($payment->manual_payment_to_account_id);
            if (!$targetAccount) {
                // Log error: Target payment account not found
                Log::error("Payment Account ID {$payment->manual_payment_to_account_id} not found for successful payment {$payment->id}.");
                return;
            }

            // Determine the category - this is important and might need more logic
            // For now, a generic "Payment Received" or based on payable_type
            $categoryName = 'Online Payment Received'; // Default
            if ($payment->payable_type) {
                $payableModelName = class_basename($payment->payable_type); // e.g., "Membership", "TrainingRegistration"
                $categoryName = $payableModelName . ' Fee';
            }

            $category = FinancialTransactionCategory::firstOrCreate(
                ['name' => $categoryName, 'type' => 'income'],
                ['description' => 'Income from ' . $categoryName, 'is_active' => true]
            );

            FinancialLedger::create([
                'ledger_entry_uuid' => (string) Str::uuid(),
                'transaction_datetime' => $payment->verified_at ?? now(),
                'entry_type' => 'income',
                'amount' => (float) $payment->amount_paid,
                'description' => "Payment received for " . ($payment->payable ? class_basename($payment->payable_type).' ID:'.$payment->payable_id : 'Misc') . ". Payment UUID: " . $payment->payment_uuid,
                'category_id' => $category->id,
                'to_payment_account_id' => $targetAccount->id,
                'payment_id' => $payment->id, // Link to the original payment
                'recorded_by_user_id' => $payment->verified_by_user_id ?? (Auth::check() ? Auth::id() : null), // User who verified
                'currency_code' => $payment->currency_code ?? 'BDT',
            ]);

            // Update the target account's balance
            $targetAccount->increment('current_balance', (float) $payment->amount_paid);
        });
    }
}
