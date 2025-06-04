<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AllowanceApplicationDocument extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'application_id',
        'document_name', // Original filename if not using Spatie's default
        'file_path',     // Path if storing manually, or use Spatie
        'document_type_id', // FK to document_types
        'description',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('allowance_documents')->singleFile();
    }

    public function application()
    {
        return $this->belongsTo(AllowanceApplication::class, 'application_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
