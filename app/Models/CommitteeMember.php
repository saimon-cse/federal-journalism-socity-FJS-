<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Changed from Relations\Pivot
use Illuminate\Database\Eloquent\Relations\Pivot;


class CommitteeMember extends Pivot // Can extend Model if it needs its own ID, etc.
{
    use HasFactory;

    protected $table = 'committee_members';
    public $incrementing = true; // If you use $table->id() in migration

    protected $fillable = [
        'committee_id',
        'user_id',
        'position_id',
        'is_manager',
        'appointed_on',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'is_manager' => 'boolean',
        'appointed_on' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(CommitteePosition::class);
    }
}
