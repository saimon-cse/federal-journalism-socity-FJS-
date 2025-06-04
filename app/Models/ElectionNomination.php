<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionNomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_position_id',
        'user_id', // Candidate
        'application_datetime',
        'status',
        'payment_id',
        'processed_by', // Admin User ID
        'processed_at',
        'processing_remarks',
        'withdrawn_at',
        'withdrawal_reason',
    ];

    protected $casts = [
        'application_datetime' => 'datetime',
        'processed_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function electionPosition()
    {
        return $this->belongsTo(ElectionPosition::class);
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class); // If direct FK
        // Or use morphOne if Payment.payable is ElectionNomination
        // return $this->morphOne(Payment::class, 'payable');
    }

    public function processedByAdmin()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
