<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ComplaintSuggestionReply extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'complaint_id',
        'user_id', // User who replied (can be submitter or admin)
        'reply_content',
        'reply_type', // 'agent' or 'user'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reply_attachments');
    }

    public function complaintSuggestion()
    {
        return $this->belongsTo(ComplaintSuggestion::class, 'complaint_id');
    }

    public function user() // User who replied
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
