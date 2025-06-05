<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    // Remove 'membership_type' string field from fillable
    protected $fillable = [
        'user_id',
        'membership_type_id', // ADD THIS
        'start_date',
        'end_date',
        'status',
        'last_payment_date',
        'next_due_date',
        'approved_by_user_id',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_payment_date' => 'datetime',
        'next_due_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ADD THIS RELATIONSHIP
    public function membershipType()
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function latestPayment()
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }

    public function activePayment()
    {
        return $this->morphOne(Payment::class, 'payable')->where('status', 'successful')->latestOfMany();
    }

    public function pendingPayment()
    {
        return $this->morphOne(Payment::class, 'payable')
            ->whereIn('status', ['pending_manual_verification', 'pending_user_action'])
            ->latestOfMany();
    }
}
