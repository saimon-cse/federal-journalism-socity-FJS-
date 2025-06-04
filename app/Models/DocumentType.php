<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'allowed_file_types',
        'max_file_size',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function allowanceTypesRequiring()
    {
        return $this->belongsToMany(AllowanceType::class, 'allowance_type_document_type');
    }

    public function allowanceApplicationDocuments()
    {
        return $this->hasMany(AllowanceApplicationDocument::class);
    }
}
