<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Example relationships based on 'type'
    public function trainingsAsSubject()
    {
        // Assuming 'type' for training subjects is 'training_subject'
        return $this->hasMany(Training::class, 'training_subject_category_id');
    }

    public function resourcePersonExpertise()
    {
        // Assuming 'type' for expertise is 'resource_person_expertise'
        // This links Category to ResourcePerson through the pivot table resource_person_expertise
        return $this->belongsToMany(ResourcePerson::class, 'resource_person_expertise');
    }
}
