<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_entry_uuid',
        'transaction_datetime',
        'entry_type',
        'amount',
        'currency_code',
        'description',
        'category_id',
        'from_payment_account_id',
        'to_payment_account_id',
        'payment_id',
        'referenceable_id',
        'referenceable_type',
        'external_party_name',
        'external_reference_id',
        'recorded_by_user_id',
        'internal_notes',
        'is_reconciled',
        'reconciled_at',
        'reconciled_by_user_id',
        'bank_statement_line_id',
    ];

    protected $casts = [
        'transaction_datetime' => 'datetime',
        'amount' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(FinancialTransactionCategory::class, 'category_id');
    }

    public function fromPaymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'from_payment_account_id');
    }

    public function toPaymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'to_payment_account_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function referenceable()
    {
        return $this->morphTo();
    }

    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function reconciledByUser()
    {
        return $this->belongsTo(User::class, 'reconciled_by_user_id');
    }
}
