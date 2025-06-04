<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name_en', 'name_bn', 'slug'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function committees()
    {
        return $this->hasMany(Committee::class, 'upazila_id');
    }

    public function electionsTargeted()
    {
        return $this->hasMany(Election::class, 'target_upazila_id');
    }

    public function announcementsTargeted()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_upazila');
    }
}
