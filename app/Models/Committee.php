<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Committee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'level',
        'description',
        'division_id',
        'district_id',
        'upazila_id',
        'is_active',
        'formation_date',
        'term_start_date',
        'term_end_date',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'formation_date' => 'date',
        'term_start_date' => 'date',
        'term_end_date' => 'date',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function members() // Through CommitteeMember model
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function committeeMembersUsers() // Direct to users via pivot
    {
        return $this->belongsToMany(User::class, 'committee_members')
                    ->using(CommitteeMember::class) // Specify the pivot model
                    ->withPivot(['position_id', 'is_manager', 'appointed_on', 'start_date', 'end_date', 'status'])
                    ->withTimestamps();
    }


    public function positions() // Positions specifically defined under this committee instance
    {
        return $this->hasMany(CommitteePosition::class);
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }
}
