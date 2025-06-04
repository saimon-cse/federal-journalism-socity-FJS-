<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowanceApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'previous_status',
        'new_status',
        'action_taken',
        'changed_by',
        'reason',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(AllowanceApplication::class, 'application_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
