<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Authenticate user and create Sanctum access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (isset($user->status) && strtolower((string) $user->status) !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive or suspended. Please contact support.',
            ], 403);
        }

        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Register a new user and generate a Sanctum access token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
            'fcm_token' => $request->fcm_token ?? null,
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful with Free Tier subscription assigned',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Log out the authenticated user by revoking the current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Get the authenticated user details.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => $request->user(),
            ],
        ], 200);
    }
}
