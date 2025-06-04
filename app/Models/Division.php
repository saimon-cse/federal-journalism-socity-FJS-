<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_bn', 'slug'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function committees()
    {
        return $this->hasMany(Committee::class, 'division_id');
    }

    public function electionsTargeted()
    {
        return $this->hasMany(Election::class, 'target_division_id');
    }

    public function announcementsTargeted()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_division');
    }

    public function trainingsTargeted()
    {
        return $this->belongsToMany(Training::class, 'training_target_divisions');
    }
}
