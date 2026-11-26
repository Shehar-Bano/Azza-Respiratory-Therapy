<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    /**
     * Start an active Free Tier (plan_id = '0') subscription if the user has no active subscription.
     */
    public static function ensureFreeTierForUser(User $user): ?self
    {
        $hasActive = self::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->exists();

        if ($hasActive) {
            return null;
        }

        $freePlan = SubscriptionPlan::where('plan_id', '0')->first();
        $durationDays = ($freePlan && $freePlan->duration_days > 0) ? (int) $freePlan->duration_days : 3650;
        $startedAt = Carbon::now();
        $expiresAt = $startedAt->copy()->addDays($durationDays);

        return self::create([
            'user_id' => $user->id,
            'plan_id' => '0',
            'cart_id' => 'FREE-TIER-' . strtoupper(Str::random(6)),
            'transaction_reference' => 'FREE-' . time() . '-' . $user->id,
            'amount' => '0.00',
            'currency' => 'USD',
            'payment_gateway' => 'Free',
            'payment_method' => 'Free',
            'payment_status' => 'success',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? null,
            'status' => 'active',
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
        ]);
    }
}
