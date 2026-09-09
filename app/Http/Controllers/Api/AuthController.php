<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Authenticate user and create Sanctum access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not registered yet.',
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
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

        if ($user->email_verified_at === null && ($user->login_method ?? 'default') === 'default') {
            return response()->json([
                'success' => false,
                'message' => 'Your email address is not verified yet. Please check your inbox and click the verification link.',
                'email_verified' => false,
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
            'login_method' => 'default',
            'fcm_token' => $request->fcm_token ?? null,
            'email_verified_at' => null,
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

        // Send Email Verification Notification
        try {
            $user->notify(new VerifyEmailNotification());
        } catch (\Exception $e) {
            Log::error('Registration email verification failed to send: ' . $e->getMessage());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! A verification link has been sent to your email address. Please verify your email before logging in.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Handle Email Verification via signed link click.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user || sha1($user->getEmailForVerification()) !== $hash) {
            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification link.',
                ], 400);
            }

            return response('
                <!DOCTYPE html>
                <html>
                <head><title>Invalid Link</title></head>
                <body style="font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #07111e; color: #f8fafc;">
                    <div style="text-align: center; background: #0f2238; padding: 2.5rem; border-radius: 16px; border: 1px solid #183554; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                        <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.15); border-radius: 50%; color: #ef4444; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; font-size: 28px; font-weight: bold;">✕</div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #ef4444;">Invalid Verification Link</h2>
                        <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.5;">The verification link is invalid or has expired.</p>
                    </div>
                </body>
                </html>
            ', 400)->header('Content-Type', 'text/html');
        }

        if ($user->hasVerifiedEmail()) {
            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Email is already verified.',
                ], 200);
            }

            return response('
                <!DOCTYPE html>
                <html>
                <head><title>Email Already Verified</title></head>
                <body style="font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #07111e; color: #f8fafc;">
                    <div style="text-align: center; background: #0f2238; padding: 2.5rem; border-radius: 16px; border: 1px solid #183554; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                        <div style="width: 60px; height: 60px; background: rgba(0, 180, 216, 0.15); border-radius: 50%; color: #00b4d8; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; font-size: 28px; font-weight: bold;">✓</div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Email Already Verified</h2>
                        <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.5;">Your email address is already verified. You can log in to the Azza Respiratory Therapy app.</p>
                    </div>
                </body>
                </html>
            ', 200)->header('Content-Type', 'text/html');
        }

        $user->markEmailAsVerified();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.',
            ], 200);
        }

        return response('
            <!DOCTYPE html>
            <html>
            <head><title>Email Verified Successfully</title></head>
            <body style="font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #07111e; color: #f8fafc;">
                <div style="text-align: center; background: #0f2238; padding: 2.5rem; border-radius: 16px; border: 1px solid #183554; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.15); border-radius: 50%; color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; font-size: 28px; font-weight: bold;">✓</div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #10b981;">Email Verified Successfully!</h2>
                    <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.5;">Thank you for verifying your email address. You can now log in to the Azza Respiratory Therapy app.</p>
                </div>
            </body>
            </html>
        ', 200)->header('Content-Type', 'text/html');
    }

    /**
     * Resend verification email to user.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not registered yet.',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email is already verified.',
            ], 200);
        }

        try {
            $user->notify(new VerifyEmailNotification());
        } catch (\Exception $e) {
            Log::error('Resend verification mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification link has been sent to your email address.',
        ], 200);
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
