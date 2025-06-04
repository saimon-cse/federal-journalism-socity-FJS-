<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingTemplateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'template_type', // e.g., 'id_card', 'certificate'
        'setting_key',
        'setting_value',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
