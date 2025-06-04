<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'title',
        'description',
        'session_datetime_start',
        'session_datetime_end',
        'duration_minutes',
        'instructor_id', // User ID of the Resource Person
        'session_materials',
        'meeting_link',
        'rank',
    ];

    protected $casts = [
        'session_datetime_start' => 'datetime',
        'session_datetime_end' => 'datetime',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function instructor() // User who is the instructor for this session
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
