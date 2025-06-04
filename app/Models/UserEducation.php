<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree_level',
        'degree_name',
        'institution_name',
        'board_or_university',
        'major_subject',
        'passing_year',
        'grade_or_cgpa',
        'is_currently_studying',
    ];

    protected $casts = [
        'passing_year' => 'integer', // Or 'date' if you store full date
        'is_currently_studying' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
