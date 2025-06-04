<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    use HasFactory;

    protected $table = 'user_experiences'; // Explicitly set table name

    protected $fillable = [
        'user_id',
        'organization_name',
        'designation',
        'responsibilities',
        'start_date',
        'end_date',
        'is_current_job',
        'employment_type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current_job' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
