<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'position_id', // FK to committee_positions
        'number_of_seats',
        'description',
        'sort_order',
        'slug',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function committeePosition() // The actual position being contested
    {
        return $this->belongsTo(CommitteePosition::class, 'position_id');
    }

    public function nominations()
    {
        return $this->hasMany(ElectionNomination::class);
    }
}
