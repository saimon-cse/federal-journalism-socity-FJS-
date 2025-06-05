<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // For potential atomic updates

class PaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_provider',
        'account_type',
        'account_identifier',
        'account_holder_name',
        'bank_name',
        'branch_name',
        'routing_number',
        'initial_balance',    // ADDED
        'current_balance',    // ADDED
        'is_active',
        'allow_user_manual_payment_to',
        'manual_payment_instructions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_user_manual_payment_to' => 'boolean',
        'initial_balance' => 'decimal:2', // ADDED
        'current_balance' => 'decimal:2', // ADDED
    ];

    // Relationships (as before)
    public function paymentMethodsDefaulted()
    {
        return $this->hasMany(PaymentMethod::class, 'default_manual_account_id');
    }

    public function paymentsToThisAccount()
    {
        return $this->hasMany(Payment::class, 'manual_payment_to_account_id');
    }

    public function financialLedgersFrom()
    {
        return $this->hasMany(FinancialLedger::class, 'from_payment_account_id');
    }

    public function financialLedgersTo()
    {
        return $this->hasMany(FinancialLedger::class, 'to_payment_account_id');
    }

    public function electionsUsingForFee()
    {
        return $this->hasMany(Election::class, 'default_payment_account_id');
    }

    public function trainingsUsingForFee()
    {
        return $this->hasMany(Training::class, 'payment_collection_account_id');
    }

    /**
     * Adjust the current balance of the account.
     * This should be called within a database transaction along with ledger entry creation.
     *
     * @param float $amount The amount to adjust by (positive for income, negative for expense)
     * @return bool
     */
    public function adjustBalance(float $amount): bool
    {
        // Using DB::raw for atomic update can be an option for high concurrency
        // For simplicity here, direct update. Ensure this is within a transaction.
        $this->current_balance = (float) $this->current_balance + $amount;
        return $this->save();
    }

    /**
     * Recalculates the current balance based on initial balance and all ledger entries.
     * This can be computationally expensive and should be used sparingly or during maintenance.
     */
    public function recalculateCurrentBalance(): void
    {
        DB::transaction(function () {
            $balance = (float) $this->initial_balance;

            // Sum of all income/transfers_in/opening_balances to this account
            $inflows = FinancialLedger::where('to_payment_account_id', $this->id)
                ->whereIn('entry_type', ['income', 'transfer', 'opening_balance', 'reconciliation_adjustment']) // Assuming adjustment can be positive
                ->sum('amount');

            // Sum of all expenses/transfers_out from this account
            $outflows = FinancialLedger::where('from_payment_account_id', $this->id)
                ->whereIn('entry_type', ['expense', 'transfer', 'reconciliation_adjustment']) // Assuming adjustment can be negative
                ->sum('amount');

            // Special handling for opening balance type, as it's already initial_balance.
            // If 'opening_balance' entries in ledger are *additional* to initial_balance, include them.
            // If 'opening_balance' ledger entry *is* the initial_balance, exclude it from inflows sum.
            // Current FinancialLedgerController logic creates 'opening_balance' as an inflow, so it adds to initial_balance.

            $this->current_balance = $balance + (float) $inflows - (float) $outflows;
            $this->saveQuietly(); // Save without triggering events if any
        });
    }
}
