<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'method_key',
        'type',
        'provider_name',
        'description',
        'logo_path',
        'is_active',
        'sort_order',
        'default_manual_account_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function defaultManualAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'default_manual_account_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
