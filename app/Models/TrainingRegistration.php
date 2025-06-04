<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TrainingRegistration extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'training_id',
        'user_id', // Applicant
        'payment_id',
        'status',
        'application_date',
        'approved_at',
        'approved_by_user_id',
        'admin_remarks',
        'certificate_issued_at',
    ];

    protected $casts = [
        'application_date' => 'datetime',
        'approved_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('training_id_cards')->singleFile();
        $this->addMediaCollection('training_certificates')->singleFile(); // If stored as files
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user() // Applicant
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class); // If direct FK
        // Or use morphOne if Payment.payable is TrainingRegistration
        // return $this->morphOne(Payment::class, 'payable');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
