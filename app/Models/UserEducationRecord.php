<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree_level',
        'degree_title',
        'major_subject',
        'institution_name',
        'graduation_year',
        'result_grade',
    ];

    /**
     * Get the user that owns the education record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
