<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ComplaintSuggestion extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'complaints_suggestions';

    protected $fillable = [
        'user_id',
        'submitted_by_name',
        'submitted_by_email',
        'type',
        'subject',
        'description',
        'is_anonymous',
        'status',
        'admin_remarks',
        'resolved_by_user_id',
        'resolved_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_attachments');
    }

    public function user() // User who submitted (if not anonymous)
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }

    public function replies()
    {
        return $this->hasMany(ComplaintSuggestionReply::class, 'complaint_id');
    }
}
