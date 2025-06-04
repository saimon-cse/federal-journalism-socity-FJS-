<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'max_amount',
        'min_amount',
        'max_applications_per_year',
        'is_active',
        // 'required_documents', // This was removed for pivot table
        'details',
        'processing_days',
        'terms_and_conditions',
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(AllowanceApplication::class);
    }

    public function requiredDocumentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'allowance_type_document_type');
    }
}
