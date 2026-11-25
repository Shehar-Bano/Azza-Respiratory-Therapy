<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Notifications\ResetPasswordLinkNotification;
use App\Notifications\ResetPasswordOtpNotification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
            try {
                $user->notify(new VerifyEmailNotification());
            } catch (\Exception $e) {
                Log::error('Login auto-resend verification link failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => false,
                'success' => false,
                'is_verified' => false,
                'verification_sent' => true,
                'message' => 'Email is not verified. A new verification link has been sent to your email.',
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
     * Register a new user, generate OTP code, and send verification email.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
            'login_method' => $request->input('login_method', 'default'),
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

        // Send Email Verification Link Notification
        try {
            $user->notify(new VerifyEmailNotification());
        } catch (\Exception $e) {
            Log::error('Registration email verification link failed to send: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Registration successful. A verification link has been sent to your email.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_verified' => false,
            ],
        ], 201);
    }

    /**
     * Verify email address via signed confirmation link clicked from email.
     */
    public function verifyEmail(Request $request, $id, $hash): View
    {
        if (!$request->hasValidSignature()) {
            return view('auth.email-verified', [
                'verified' => false,
                'message' => 'This confirmation link has expired or is invalid. Please request a new confirmation email.',
            ]);
        }

        $user = User::find($id);
        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return view('auth.email-verified', [
                'verified' => false,
                'message' => 'Invalid email verification link.',
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('auth.email-verified', [
            'verified' => true,
            'message' => 'Your email has been verified! You can now return to the Azza Respiratory Therapy app and tap Continue to home.',
        ]);
    }

    /**
     * Check email verification status and auto-login if verified.
     */
    public function checkVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'fcm_token' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'is_verified' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'is_verified' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'status' => false,
                'is_verified' => false,
                'message' => 'Your email is not verified yet. Please click the verification link sent to your inbox.',
            ], 403);
        }

        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $activeSub = SubscriptionTransaction::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->latest()
            ->first();

        $planId = $activeSub ? (string) $activeSub->plan_id : '0';
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'is_verified' => true,
            'message' => 'Email verified successfully.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'plan_id' => $planId,
            ],
        ], 200);
    }

    /**
     * Verify email address using 6-digit OTP code.
     */
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = DB::table('email_verification_otps')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No verification request found for this email address.',
            ], 404);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The verification OTP code has expired. Please request a new one.',
            ], 400);
        }

        if ((string) $record->otp !== (string) $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification OTP code. Please check your email and try again.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Mark user email as verified
        $user->markEmailAsVerified();

        // Delete used OTP
        DB::table('email_verification_otps')->where('email', $request->email)->delete();

        // Generate or fetch user auth token
        $token = $user->createToken('auth_token')->plainTextToken;
        $freshUser = $user->fresh();

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Email verified successfully.',
            'token' => $token,
            'user' => $freshUser,
            'data' => [
                'user' => $freshUser,
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Resend verification OTP code to user email.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

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
            Log::error('Resend verification email failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'A new verification link has been sent to your email.',
        ], 200);
    }

    /**
     * Send password reset link to user email.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => "We can't find a user with that email address.",
            ], 404);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            $user->notify(new ResetPasswordLinkNotification($token, $user->email));
        } catch (\Exception $e) {
            Log::error('Forgot password reset link email failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'We have emailed your password reset link.',
        ], 200);
    }

    /**
     * Show WebView form for resetting password via email link.
     */
    public function showResetPasswordWebView(Request $request, string $token): View
    {
        return view('auth.reset-password-webview', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Handle WebView password reset form submission.
     */
    public function submitResetPasswordWebView(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(60)->isPast() || !Hash::check($request->token, $record->token)) {
            return back()->with('reset_error', 'This password reset link is invalid or has expired. Please request a new one.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('reset_error', 'User not found.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->tokens()->delete();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return back()->with('reset_success', 'Password reset successfully! You can now return to the Azza Respiratory Therapy app and sign in.');
    }

    /**
     * Verify the password reset OTP code.
     */
    public function verifyResetOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No password reset request found for this email.',
            ], 404);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The OTP code has expired. Please request a new one.',
            ], 400);
        }

        if ((string) $record->token !== (string) $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code. Please check your email and try again.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not registered yet.',
            ], 404);
        }

        // Delete used reset OTP
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Create reset token for authentication
        $token = $user->createToken('PasswordResetToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $token,
            'token' => $token,
        ], 200);
    }

    /**
     * Reset user password after verifying OTP.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            $tokenString = $request->bearerToken() ?? $request->header('token') ?? $request->token;
            if ($tokenString) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenString);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                }
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please verify OTP code first and include Bearer token.',
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke temporary tokens
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Your password has been reset successfully.',
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
     * Get the authenticated user details including latest vital & lab.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load(['latestVital', 'latestLab', 'activeSubscription.plan']);

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => $user,
            ],
        ], 200);
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', 'string', 'max:20'],
            'sex' => ['nullable', 'string', 'max:20'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'bmi' => ['nullable', 'numeric', 'min:0'],
            'fcm_token' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [];

        if ($request->filled('name')) {
            $data['name'] = $request->input('name');
        }

        if ($request->has('age')) {
            $data['age'] = $request->input('age');
        }

        $genderValue = $request->input('gender') ?? $request->input('sex');
        if ($genderValue !== null) {
            $data['gender'] = $genderValue;
        }

        if ($request->has('height')) {
            $data['height'] = $request->input('height');
        }

        if ($request->has('weight')) {
            $data['weight'] = $request->input('weight');
        }

        // Calculate or assign BMI
        if ($request->has('bmi') && $request->input('bmi') !== null) {
            $data['bmi'] = $request->input('bmi');
        } elseif (isset($data['height']) && isset($data['weight']) && (float)$data['height'] > 0) {
            $heightInMeters = (float)$data['height'] / 100;
            $data['bmi'] = round((float)$data['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        if ($request->filled('fcm_token')) {
            $data['fcm_token'] = $request->input('fcm_token');
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user->fresh(['latestVital', 'latestLab', 'activeSubscription.plan']),
            ],
        ], 200);
    }
}
