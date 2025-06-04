<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocialMedia extends Model
{
    use HasFactory;

    protected $table = 'user_social_medias'; // Explicitly set table name

    protected $fillable = [
        'user_id',
        'platform_name',
        'profile_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
