<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'dob',
        'blood_group',
        'gender',
        'religion',
        'phone_primary',
        'phone_secondary',
        'whatsapp_number',
        'nid_number',
        'passport_number',
        'newsletter_subscribed',
        'is_profile_public',
        'workplace_type',
    ];

    protected $casts = [
        'dob' => 'date',
        'newsletter_subscribed' => 'boolean',
        'is_profile_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
