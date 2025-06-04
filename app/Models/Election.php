<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'election_date',
        'nomination_start_datetime',
        'nomination_end_datetime',
        'withdrawal_end_datetime',
        'results_declared_at',
        'committee_id',
        'level',
        'target_division_id',
        'target_district_id',
        'target_upazila_id',
        'nomination_fee',
        'default_payment_account_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'election_date' => 'date',
        'nomination_start_datetime' => 'datetime',
        'nomination_end_datetime' => 'datetime',
        'withdrawal_end_datetime' => 'datetime',
        'results_declared_at' => 'datetime',
        'nomination_fee' => 'decimal:2',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function targetDivision()
    {
        return $this->belongsTo(Division::class, 'target_division_id');
    }

    public function targetDistrict()
    {
        return $this->belongsTo(District::class, 'target_district_id');
    }

    public function targetUpazila()
    {
        return $this->belongsTo(Upazila::class, 'target_upazila_id');
    }

    public function defaultPaymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'default_payment_account_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions()
    {
        return $this->hasMany(ElectionPosition::class);
    }
}
