<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['division_id', 'name_en', 'name_bn', 'slug'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function upazilas()
    {
        return $this->hasMany(Upazila::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function committees()
    {
        return $this->hasMany(Committee::class, 'district_id');
    }

    public function electionsTargeted()
    {
        return $this->hasMany(Election::class, 'target_district_id');
    }

    public function announcementsTargeted()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_district');
    }

    public function trainingsTargeted()
    {
        return $this->belongsToMany(Training::class, 'training_target_districts');
    }
}
