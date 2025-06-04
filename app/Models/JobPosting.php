<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobPosting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image_path', // Or handled by Spatie
        'application_deadline',
        'apply_instructions',
        'posted_by',
        'status',
    ];

    protected $casts = [
        'application_deadline' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('job_banners')->singleFile();
    }

    public function postedByUser()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
