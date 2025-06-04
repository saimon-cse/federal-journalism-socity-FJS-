<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
        'allow_user_manual_payment_to',
        'manual_payment_instructions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_user_manual_payment_to' => 'boolean',
    ];

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
}
