<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'name_en',
        'name_bn',
    ];

    /**
     * Get the district that owns the upazila.
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the user addresses for the upazila.
     * (Optional, if you need to query users by upazila directly)
     */
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
