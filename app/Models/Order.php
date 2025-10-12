<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'delivery_agent_id',
        'status',
        'total_amount',
        'meta',
        'payment_status',
        'payment_method',
        'transaction_id',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_zip',
        'latitude',
        'longitude',
        'estimated_delivery_at',
        'delivered_at',
        'order_number',
        'delivery_fee',
        'discount_amount',
        'final_amount',
    ];

    public $casts = [
        'meta' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }
}
