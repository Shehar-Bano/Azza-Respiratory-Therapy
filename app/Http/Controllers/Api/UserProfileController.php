<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Models\UserFeedback;
use App\Models\UserLab;
use App\Models\UserVital;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    /**
     * Update authenticated user's profile, location, and avatar.
     * Supports POST /api/user/update-profile, POST /api/user/profile, PUT /api/user/profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            // Backward-compatible health profile fields
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
                'status' => false,
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [];

        foreach (['name', 'email', 'phone', 'location', 'city', 'fcm_token', 'age', 'height', 'weight'] as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                if (in_array($field, ['name', 'email'], true) && ($val === null || $val === '')) {
                    continue;
                }
                $data[$field] = $val;
            }
        }

        if ($request->has('latitude') && $request->input('latitude') !== null && $request->input('latitude') !== '') {
            $data['latitude'] = (float) $request->input('latitude');
        }
        if ($request->has('longitude') && $request->input('longitude') !== null && $request->input('longitude') !== '') {
            $data['longitude'] = (float) $request->input('longitude');
        }

        $genderValue = $request->input('gender') ?? $request->input('sex');
        if ($genderValue !== null) {
            $data['gender'] = $genderValue;
        }

        if ($request->has('bmi') && $request->input('bmi') !== null) {
            $data['bmi'] = $request->input('bmi');
        } elseif (isset($data['height']) && isset($data['weight']) && (float) $data['height'] > 0) {
            $heightInMeters = (float) $data['height'] / 100;
            $data['bmi'] = round((float) $data['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        if ($request->hasFile('profile_image')) {
            $avatarDir = public_path('images/avatars');
            if (!File::isDirectory($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true);
            }

            // Delete previous local avatar if present
            if (!empty($user->profile_image) && !str_starts_with($user->profile_image, 'http')) {
                $oldPath = public_path($user->profile_image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file = $request->file('profile_image');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = 'user_' . $user->id . '_' . time() . '.' . $ext;
            $file->move($avatarDir, $filename);
            $data['profile_image'] = 'images/avatars/' . $filename;
        }

        if (!empty($data)) {
            $user->update($data);
        }

        $freshUser = $user->fresh();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $freshUser->id,
                'name' => $freshUser->name,
                'email' => $freshUser->email,
                'phone' => $freshUser->phone,
                'location' => $freshUser->location,
                'city' => $freshUser->city,
                'latitude' => $freshUser->latitude !== null ? (float) $freshUser->latitude : null,
                'longitude' => $freshUser->longitude !== null ? (float) $freshUser->longitude : null,
                'profile_image' => $freshUser->profile_image_url,
                'email_verified_at' => $freshUser->email_verified_at?->toISOString(),
                'updated_at' => $freshUser->updated_at?->toISOString(),
            ],
        ], 200);
    }

    /**
     * Get the authenticated user's profile, location, and active subscription.
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $activeSub = SubscriptionTransaction::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->latest()
            ->first();

        $planId = $activeSub ? (string) $activeSub->plan_id : '0';
        $planName = $activeSub
            ? ($activeSub->plan ? $activeSub->plan->title : ('Plan ' . $activeSub->plan_id))
            : 'Free Plan';
        $subStatus = $activeSub ? 'active' : 'inactive';
        $expiresAt = $activeSub && $activeSub->expires_at
            ? $activeSub->expires_at->format('Y-m-d')
            : null;

        return response()->json([
            'status' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'location' => $user->location,
                'city' => $user->city,
                'latitude' => $user->latitude !== null ? (float) $user->latitude : null,
                'longitude' => $user->longitude !== null ? (float) $user->longitude : null,
                'profile_image' => $user->profile_image_url,
                'plan_id' => $planId,
                'subscription' => [
                    'status' => $subStatus,
                    'plan_name' => $planName,
                    'expires_at' => $expiresAt,
                ],
                'created_at' => $user->created_at?->toISOString(),
                'updated_at' => $user->updated_at?->toISOString(),
            ],
        ], 200);
    }

    /**
     * Permanently delete the authenticated user's account and all associated personal data.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->filled('reason')) {
            Log::info('User account deletion requested', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $request->input('reason'),
            ]);
        }

        // Remove uploaded avatar file from media storage
        if (!empty($user->profile_image) && !str_starts_with($user->profile_image, 'http')) {
            $avatarPath = public_path($user->profile_image);
            if (File::exists($avatarPath)) {
                File::delete($avatarPath);
            }
        }

        DB::transaction(function () use ($user) {
            // Revoke all active Sanctum tokens
            $user->tokens()->delete();

            // Cancel/remove subscription records
            SubscriptionTransaction::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);
            SubscriptionTransaction::where('user_id', $user->id)->delete();

            // Delete associated personal records
            AppNotification::where('user_id', $user->id)->delete();
            UserVital::where('user_id', $user->id)->delete();
            UserLab::where('user_id', $user->id)->delete();
            UserFeedback::where('user_id', $user->id)->delete();

            DB::table('email_verification_otps')->where('email', $user->email)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            $user->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'User account and all associated personal data have been permanently deleted.',
        ], 200);
    }

    /**
     * Get notification channel preferences for the authenticated user.
     */
    public function getNotificationPreferences(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()->getNotificationPreferences(),
        ], 200);
    }

    /**
     * Update notification channel preferences for the authenticated user.
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'push_enabled' => ['required', 'boolean'],
            'channels' => ['required', 'array'],
            'channels.General Notification' => ['sometimes', 'boolean'],
            'channels.Reminder' => ['sometimes', 'boolean'],
            'channels.Update' => ['sometimes', 'boolean'],
            'channels.Security Alert' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $current = $user->getNotificationPreferences()['channels'];
        $incoming = $request->input('channels', []);

        foreach (array_keys(User::defaultNotificationChannels()) as $channelName) {
            if (array_key_exists($channelName, $incoming)) {
                $current[$channelName] = (bool) $incoming[$channelName];
            }
        }

        $user->update([
            'push_enabled' => (bool) $request->boolean('push_enabled'),
            'notification_channels' => $current,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification preferences updated successfully.',
            'data' => $user->fresh()->getNotificationPreferences(),
        ], 200);
    }
}
