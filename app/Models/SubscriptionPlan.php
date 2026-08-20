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
        'feature_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'feature_ids' => 'array',
        'duration_days' => 'integer',
    ];

    /**
     * Get feature objects for this plan.
     */
    public function getFeatureObjectsAttribute()
    {
        if (empty($this->feature_ids) || !is_array($this->feature_ids)) {
            return collect();
        }

        return SubscriptionFeature::whereIn('id', $this->feature_ids)->get();
    }
}
