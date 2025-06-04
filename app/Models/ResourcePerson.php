<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ResourcePerson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'bio',
        'is_publicly_listed',
        'standard_honorarium_rate',
        'preferred_payment_details',
        'status',
        'admin_remarks',
        'approved_by_user_id',
        'approved_at',
    ];

    protected $casts = [
        'is_publicly_listed' => 'boolean',
        'standard_honorarium_rate' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('resource_person_cvs')->singleFile();
        // Potentially other collections like 'presentations' if general to the RP
    }

    public function user() // The User account associated with this RP profile
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function expertiseCategories()
    {
        // Assumes 'resource_person_expertise' is the pivot table name
        return $this->belongsToMany(Category::class, 'resource_person_expertise', 'resource_person_id', 'category_id');
    }

    public function contents()
    {
        return $this->hasMany(ResourcePersonContent::class);
    }

    public function trainingInvitations()
    {
        return $this->hasMany(TrainingInvitation::class, 'resource_person_id', 'user_id'); // Assuming resource_person_id in invitations maps to user_id of RP
    }

    public function trainingSessionsInstructing()
    {
         return $this->hasMany(TrainingSession::class, 'instructor_id', 'user_id'); // Assuming instructor_id in sessions maps to user_id of RP
    }

     public function trainingsInstructingAs() {
        return $this->hasMany(TrainingInstructor::class, 'instructor_id', 'user_id');
    }
}
