<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_type', // 'permanent', 'present', 'work'
        'division_id',
        'district_id',
        'upazila_id',
        'address_line1',
        'address_line2',
        'postal_code',
        'is_primary', // boolean
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the division for the address.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the district for the address.
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the upazila for the address.
     */
    public function upazila()
    {
        return $this->belongsTo(Upazila::class);
    }
}
