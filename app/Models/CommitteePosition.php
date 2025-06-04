<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'created_by', // user_id
        'committee_id', // if positions are tied to specific committee *instances*
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // If committee_id in committee_positions refers to a specific committee instance
    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function committeeMembers()
    {
        return $this->hasMany(CommitteeMember::class, 'position_id');
    }

    public function electionPositions()
    {
        return $this->hasMany(ElectionPosition::class, 'position_id');
    }
}
