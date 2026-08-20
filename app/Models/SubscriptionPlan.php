<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'plan_id',
        'title',
        'price',
        'price_usd',
        'price_sar',
        'currency',
        'currency_usd',
        'currency_sar',
        'duration_days',
        'access',
        'features',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'features' => 'array',
        'duration_days' => 'integer',
    ];
}
