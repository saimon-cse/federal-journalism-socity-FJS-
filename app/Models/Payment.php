<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'payment_uuid',
        'user_id',
        'payer_name',
        'payer_email',
        'payer_phone',
        'payable_id',
        'payable_type',
        'amount_due',
        'amount_paid',
        'currency_code',
        'discount_amount',
        'vat_tax_amount',
        'net_amount_payable',
        'payment_method_id',
        'manual_transaction_id_user',
        'manual_payment_datetime_user',
        'manual_payment_to_account_id',
        'gateway_name',
        'gateway_transaction_id',
        'gateway_checkout_url',
        'gateway_initiated_at',
        'gateway_response_at',
        'status',
        'verified_by_user_id',
        'verified_at',
        'verification_remarks',
        'notes',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_tax_amount' => 'decimal:2',
        'net_amount_payable' => 'decimal:2',
        'manual_payment_datetime_user' => 'datetime',
        'gateway_initiated_at' => 'datetime',
        'gateway_response_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_proofs')->singleFile();
    }

    public function user() // Payer
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function manualPaymentToAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'manual_payment_to_account_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function financialLedgerEntries()
    {
        return $this->hasMany(FinancialLedger::class);
    }

    public function paymentGatewayLogs()
    {
        return $this->hasMany(PaymentGatewayLog::class);
    }

    // Inverse relations for specific payable types (optional but can be handy)
    public function electionNomination()
    {
        if ($this->payable_type === ElectionNomination::class) {
            return $this->belongsTo(ElectionNomination::class, 'payable_id');
        }
        return null;
    }

    public function allowancePaymentRecord() // Note: This is a Payment model's relation
    {
         if ($this->payable_type === AllowanceApplication::class) { // Or directly link if AllowancePayment makes a Payment
            return $this->hasOne(AllowancePayment::class); // If AllowancePayment has a payment_id
        }
        return null;
    }


    public function trainingRegistration()
    {
        if ($this->payable_type === TrainingRegistration::class) {
            return $this->belongsTo(TrainingRegistration::class, 'payable_id');
        }
        return null;
    }

     public function membershipRecord()
    {
        if ($this->payable_type === Membership::class) {
            return $this->belongsTo(Membership::class, 'payable_id');
        }
        return null;
    }
}
