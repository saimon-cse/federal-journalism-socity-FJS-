<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'name_en',
        'name_bn',
    ];

    /**
     * Get the division that owns the district.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the upazilas for the district.
     */
    public function upazilas()
    {
        return $this->hasMany(Upazila::class);
    }

    /**
     * Get the user addresses for the district.
     * (Optional, if you need to query users by district directly)
     */
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
