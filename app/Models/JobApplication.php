<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobApplication extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'job_posting_id',
        'user_id',
        'cv', // Path or handled by Spatie
        'status',
        'expected_salary',
        'available_start_date',
        'notes_for_hiring_manager',
        'applied_at',
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
        'available_start_date' => 'date',
        'applied_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cv_documents')->singleFile();
        $this->addMediaCollection('applicant_nid_documents')->singleFile(); // If NID is specific to application
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function user() // Applicant
    {
        return $this->belongsTo(User::class);
    }
}
