<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'gateway_name',
        'log_type',
        'direction',
        'url_endpoint',
        'request_headers',
        'request_payload',
        'response_status_code',
        'response_headers',
        'response_payload',
        'error_message',
    ];

    // Consider casting payloads to 'array' if they are always JSON strings
    // protected $casts = [
    //     'request_headers' => 'array',
    //     'request_payload' => 'array',
    //     'response_headers' => 'array',
    //     'response_payload' => 'array',
    // ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
