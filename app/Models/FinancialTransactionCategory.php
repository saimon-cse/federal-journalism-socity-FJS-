<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
        'parent_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parentCategory()
    {
        return $this->belongsTo(FinancialTransactionCategory::class, 'parent_category_id');
    }

    public function subCategories()
    {
        return $this->hasMany(FinancialTransactionCategory::class, 'parent_category_id');
    }

    public function financialLedgers()
    {
        return $this->hasMany(FinancialLedger::class, 'category_id');
    }
}
