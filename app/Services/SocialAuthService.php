<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SocialAuthService
{
    /**
     * Handle social login logic: find or create user and generate Sanctum token.
     *
     * @param array{name?: string, email: string, login_method: string, password?: string, fcm_token?: string} $data
     * @return array{user: User, token: string}
     */
    public function handleSocialLogin(array $data): array
    {
        $loginMethod = $data['login_method'] ?? 'default';
        $user = User::where('email', $data['email'])->first();

        if ($loginMethod === 'default') {
            if (!$user) {
                throw new InvalidArgumentException('User is not registered yet.');
            }

            if (!empty($data['password']) && !Hash::check($data['password'], $user->password)) {
                throw new InvalidArgumentException('Invalid password.');
            }

            if ($user->email_verified_at === null) {
                throw new InvalidArgumentException('Your email address is not verified yet. Please check your inbox and verify your email.');
            }

            $updateData = ['login_method' => $loginMethod];
            if (!empty($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            $user->update($updateData);
        } else {
            if ($user) {
                $updateData = [
                    'login_method' => $loginMethod,
                    'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
                ];
                if (!empty($data['name'])) {
                    $updateData['name'] = $data['name'];
                }
                $user->update($updateData);
            } else {
                $name = !empty($data['name']) ? $data['name'] : 'User';

                $user = User::create([
                    'name' => $name,
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'user',
                    'status' => 'active',
                    'login_method' => $loginMethod,
                    'email_verified_at' => Carbon::now(),
                ]);

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
            }
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
