<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowanceApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'user_id',
        'allowance_type_id',
        'applied_amount',
        'approved_amount',
        'disbursed_amount',
        'currency_code',
        'reason',
        'additional_details',
        'status',
        'priority',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'payment_date',
        'payment_notes',
        'application_date',
        'expected_completion_date',
    ];

    protected $casts = [
        'applied_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'disbursed_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'application_date' => 'date',
        'expected_completion_date' => 'date',
    ];

    public function user() // Applicant
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function allowanceType()
    {
        return $this->belongsTo(AllowanceType::class);
    }

    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(AllowanceApplicationDocument::class, 'application_id');
    }

    public function allowancePayments() // Payments made specifically for this allowance
    {
        return $this->hasMany(AllowancePayment::class, 'application_id');
    }

    // If using generic Payment table for allowance disbursal
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }


    public function reviews()
    {
        return $this->hasMany(AllowanceApplicationReview::class, 'application_id');
    }

    public function logs()
    {
        return $this->hasMany(AllowanceApplicationLog::class, 'application_id');
    }
}
