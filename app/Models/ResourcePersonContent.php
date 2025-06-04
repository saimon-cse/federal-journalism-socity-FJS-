<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ResourcePersonContent extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'resource_person_id',
        'title',
        'description',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'admin_remarks',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('content_files')->singleFile();
    }

    public function resourcePerson()
    {
        return $this->belongsTo(ResourcePerson::class);
    }

    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
