<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; // Using Pivot for many-to-many intermediate

class TrainingInstructor extends Pivot
{
    use HasFactory;

    protected $table = 'training_instructors';
    public $incrementing = true; // If you use $table->id() in migration

    protected $fillable = [
        'training_id',
        'instructor_id', // User ID of the instructor
        'role_in_training',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function instructorUser() // The User who is an instructor
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
