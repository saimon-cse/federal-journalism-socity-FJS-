<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_type',
        'account_identifier',
        'account_holder_name',
        'bank_name',
        'branch_name',
        'routing_number',
        'instructions_for_payer',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'payment_account_id_received_in');
    }

    public function getDisplayTypeAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->account_type));
    }
}
