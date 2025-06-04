<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCertificateSignatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'name',
        'designation',
        'sort_order',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
