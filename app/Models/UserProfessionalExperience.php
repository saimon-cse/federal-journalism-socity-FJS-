<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfessionalExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation',
        'organization_name',
        'start_date',
        'end_date',
        'is_current_job', // boolean
        'responsibilities',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current_job' => 'boolean',
    ];

    /**
     * Get the user that owns the professional experience.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
