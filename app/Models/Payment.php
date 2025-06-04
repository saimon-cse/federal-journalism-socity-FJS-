<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payable_id',
        'payable_type',
        'payment_account_id_received_in',
        'amount',
        'currency',
        'purpose',
        'external_transaction_id',
        'payment_method_used',
        'payment_proof_path',
        'payment_date',
        'status',
        'verified_by_user_id',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user who made the payment.
     */
    public function payer() // Renamed from user() to avoid conflict if payable_type is User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent payable model (User for membership, TrainingRegistration, etc.).
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Get the payment account where the payment was received.
     */
    public function paymentAccountReceivedIn()
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id_received_in');
    }

    /**
     * Get the user who verified the payment.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
