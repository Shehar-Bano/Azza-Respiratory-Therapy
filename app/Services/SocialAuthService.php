<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthService
{
    /**
     * Handle social login logic: find or create user and generate Sanctum token.
     *
     * @param array{name: string, email: string} $data
     * @return array{user: User, token: string}
     */
    public function handleSocialLogin(array $data): array
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        if ($user->wasRecentlyCreated) {
            // Assign Free Tier (plan_id: '0') Subscription
            $freePlan = SubscriptionPlan::where('plan_id', '0')->first();
            $durationDays = ($freePlan && $freePlan->duration_days > 0) ? $freePlan->duration_days : 3650;
            $startedAt = Carbon::now();
            $expiresAt = $startedAt->copy()->addDays($durationDays);

            SubscriptionTransaction::create([
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
                'status' => 'active',
                'started_at' => $startedAt,
                'expires_at' => $expiresAt,
            ]);
        } elseif ($user->name !== $data['name']) {
            // Update name if user exists and name changed
            $user->update(['name' => $data['name']]);
        }

        if (!empty($data['fcm_token'])) {
            $user->update(['fcm_token' => $data['fcm_token']]);
        }

        $token = $user->createToken('social_login_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
