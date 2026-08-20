<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionTransaction extends Model
{
    use HasFactory;

    protected $table = 'subscription_transactions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'cart_id',
        'transaction_reference',
        'amount',
        'currency',
        'payment_gateway',
        'payment_method',
        'card_brand',
        'card_first_six',
        'card_last_four',
        'payment_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'gateway_response',
        'status',
        'started_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gateway_response' => 'array',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user associated with the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the plan associated with the transaction.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id', 'plan_id');
    }
}
