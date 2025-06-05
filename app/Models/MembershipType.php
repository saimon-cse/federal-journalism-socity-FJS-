<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_active',
        'monthly_amount',
        'annual_amount',
        'is_recurring',
        'membership_duration', // e.g., "12 months", "1 month", "Lifetime"
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'monthly_amount' => 'decimal:2',
        'annual_amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    // Helper to get the relevant fee based on a chosen payment cycle (e.g., 'monthly' or 'annual')
    public function getFeeForCycle(string $cycle = 'annual'): ?float
    {
        if ($cycle === 'monthly' && $this->monthly_amount !== null) {
            return (float) $this->monthly_amount;
        }
        if ($cycle === 'annual' && $this->annual_amount !== null) {
            return (float) $this->annual_amount;
        }
        // Default to annual if monthly not available or invalid cycle, or if only annual is set
        return $this->annual_amount !== null ? (float) $this->annual_amount : ($this->monthly_amount !== null ? (float) $this->monthly_amount : null);
    }

    // Helper to parse duration string (e.g., "12 months") into CarbonInterval or similar
    public function getDurationInMonths(): ?int
    {
        if (!$this->membership_duration || strtolower($this->membership_duration) === 'lifetime') {
            return null;
        }
        // Simple parsing, can be made more robust
        preg_match('/(\d+)\s*(month|year)s?/i', $this->membership_duration, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $value = (int) $matches[1];
            $unit = strtolower($matches[2]);
            if ($unit === 'year') {
                return $value * 12;
            }
            return $value;
        }
        return null; // Or throw an exception for invalid format
    }
}
