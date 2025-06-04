<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'resource_person_id', // User ID of the Resource Person
        'invited_by', // User ID of Admin
        'invitation_message',
        'offered_honorarium',
        'status',
        'responded_at',
        'response_remarks',
    ];

    protected $casts = [
        'offered_honorarium' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function resourcePersonUser() // The User who is the Resource Person
    {
        return $this->belongsTo(User::class, 'resource_person_id');
    }

    public function invitedByUser() // Admin who sent invitation
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
