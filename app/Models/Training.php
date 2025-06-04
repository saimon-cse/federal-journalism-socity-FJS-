<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Training extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'batch_identifier',
        'training_subject_category_id',
        'description',
        'mode',
        'venue_details',
        'start_datetime',
        'end_datetime',
        'total_duration_hours',
        'total_sessions',
        'payment_type',
        'fee_amount',
        'payment_collection_account_id',
        'is_open_for_members',
        'is_open_for_non_members',
        'max_participants',
        'application_deadline',
        'status',
        'video_youtube_url',
        // 'custom_id_card_settings', // Removed for separate table
        // 'custom_certificate_settings', // Removed for separate table
        'created_by_user_id',
        'copied_from_training_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'fee_amount' => 'decimal:2',
        'is_open_for_members' => 'boolean',
        'is_open_for_non_members' => 'boolean',
        'application_deadline' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('training_banners')->singleFile();
        $this->addMediaCollection('training_materials');
    }

    public function trainingSubjectCategory()
    {
        return $this->belongsTo(Category::class, 'training_subject_category_id');
    }

    public function paymentCollectionAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_collection_account_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function copiedFromTraining()
    {
        return $this->belongsTo(Training::class, 'copied_from_training_id');
    }

    public function targetDivisions()
    {
        return $this->belongsToMany(Division::class, 'training_target_divisions');
    }

    public function targetDistricts()
    {
        return $this->belongsToMany(District::class, 'training_target_districts');
    }

    public function certificateSignatories()
    {
        return $this->hasMany(TrainingCertificateSignatory::class);
    }

    public function sessions()
    {
        return $this->hasMany(TrainingSession::class)->orderBy('rank');
    }

    public function instructors() // Through TrainingInstructor model
    {
        return $this->hasMany(TrainingInstructor::class);
    }

    public function instructorUsers() // Direct to users via pivot
    {
        return $this->belongsToMany(User::class, 'training_instructors', 'training_id', 'instructor_id')
                    ->using(TrainingInstructor::class)
                    ->withPivot('role_in_training')
                    ->withTimestamps();
    }


    public function registrations()
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function invitations()
    {
        return $this->hasMany(TrainingInvitation::class);
    }

    public function templateSettings()
    {
        return $this->hasMany(TrainingTemplateSetting::class);
    }
}
