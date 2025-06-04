<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_bn',
    ];

    /**
     * Get the districts for the division.
     */
    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
