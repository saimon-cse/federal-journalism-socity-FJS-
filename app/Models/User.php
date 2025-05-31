<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // From Spatie
use Illuminate\Database\Eloquent\SoftDeletes; // For soft deletes

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profile_picture_path',
        'is_member',
        'membership_start_date',
        'membership_expires_on',
        'newsletter_subscribed',
        'is_profile_public',
        'user_type',
        'email_verified_at', // Added this
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_member' => 'boolean',
        'newsletter_subscribed' => 'boolean',
        'is_profile_public' => 'boolean',
        'membership_start_date' => 'date',
        'membership_expires_on' => 'date',
    ];

    // One-to-One relationship with UserProfile
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // One-to-Many relationship with UserAddress
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    // One-to-Many relationship with UserEducationRecord
    public function educationRecords()
    {
        return $this->hasMany(UserEducationRecord::class);
    }

    // One-to-Many relationship with UserProfessionalExperience
    public function professionalExperiences()
    {
        return $this->hasMany(UserProfessionalExperience::class);
    }

    // One-to-Many relationship with UserSocialLink
    public function socialLinks()
    {
        return $this->hasMany(UserSocialLink::class);
    }

    // Add this if you have an 'avatar' attribute and want to get a URL
    public function getAvatarUrlAttribute()
    {
        if ($this->profile_picture_path) {
            return asset('storage/' . $this->profile_picture_path);
        }
        // You might want a default avatar from settings or a placeholder
        return asset(Setting::get('default_user_avatar', 'backend/assets/images/default-avatar.png'));
    }
}
