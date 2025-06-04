<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowancePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'payment_id', // FK to the actual record in 'payments' table
        'payment_uuid', // a unique ID for this allowance payment transaction itself
        'amount',
        'payment_method',
        'payment_reference',
        'payment_details',
        'status',
        'payment_date',
        'processed_by',
        'notes',
        'receipt_path', // Or use Spatie Media Library
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(AllowanceApplication::class, 'application_id');
    }

    public function paymentRecord() // The actual transaction in the main payments table
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
