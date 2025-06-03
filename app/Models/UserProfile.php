<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'father_name', 'mother_name', 'date_of_birth',
        'blood_group', 'gender', 'religion', 'whatsapp_number',
        'nid_number', 'nid_path', 'passport_number', 'passport_path',
        'workplace_type', 'bio',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
