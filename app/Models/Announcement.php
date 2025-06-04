<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'posted_by',
        'publish_at',
        'expires_at',
        'target_audience_type',
        'send_email_notification',
        'show_on_dashboard',
        'is_active',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
        'send_email_notification' => 'boolean',
        'show_on_dashboard' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function postedByUser()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Relationships for pivot tables (created due to JSON removal)
    public function targetRoles()
    {
        // Assumes 'roles' table from Spatie Permissions
        return $this->belongsToMany(Role::class, 'announcement_role');
    }

    public function targetDivisions()
    {
        return $this->belongsToMany(Division::class, 'announcement_division');
    }

    public function targetDistricts()
    {
        return $this->belongsToMany(District::class, 'announcement_district');
    }

    public function targetUpazilas()
    {
        return $this->belongsToMany(Upazila::class, 'announcement_upazila');
    }

    public function targetUsers()
    {
        return $this->belongsToMany(User::class, 'announcement_user');
    }
}
