<?php

namespace App\Services;

use App\Models\User;
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

        // Update name if user exists and name changed
        if ($user->wasRecentlyCreated === false && $user->name !== $data['name']) {
            $user->update(['name' => $data['name']]);
        }

        $token = $user->createToken('social_login_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
