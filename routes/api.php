<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\HealthRecordApiController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\SubscriptionFeatureApiController;
use App\Http\Controllers\Api\SubscriptionPlanApiController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\V1\AppNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication & OTP Endpoints
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/check-verification', [AuthController::class, 'checkVerification']);
Route::post('/email/resend-verification', [AuthController::class, 'resendVerificationEmail']);
Route::post('/verify-email-otp', [AuthController::class, 'verifyEmailOtp']);
Route::post('/verify-email', [AuthController::class, 'verifyEmailOtp']);
Route::post('/email/resend-otp', [AuthController::class, 'resendVerificationEmail']);
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public Support Contact & FAQs
Route::get('/support-contact', [SupportController::class, 'getSupportContact']);
Route::get('/faqs', [SupportController::class, 'getFaqs']);

// Public Clinical Content & Catalog APIs
Route::get('/article/get', [ArticleApiController::class, 'getArticles']);
Route::get('/card/get', [CardApiController::class, 'getCards']);
Route::get('/category/get', [CategoryApiController::class, 'getCategories']);
Route::get('/category/artical/get', [ArticleApiController::class, 'getCategoryArticles']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // User Profile, Location & Account Deletion
    Route::get('/user/profile', [UserProfileController::class, 'getProfile']);
    Route::post('/user/profile', [UserProfileController::class, 'updateProfile']);
    Route::put('/user/profile', [UserProfileController::class, 'updateProfile']);
    Route::post('/user/update-profile', [UserProfileController::class, 'updateProfile']);
    Route::delete('/user/delete-account', [UserProfileController::class, 'deleteAccount']);
    Route::post('/user/delete-account', [UserProfileController::class, 'deleteAccount']);

    // Notification Channel Preferences
    Route::get('/user/notification-preferences', [UserProfileController::class, 'getNotificationPreferences']);
    Route::post('/user/notification-preferences', [UserProfileController::class, 'updateNotificationPreferences']);
    Route::post('/user/fcm-token', [AppNotificationController::class, 'updateFcmToken']);

    // User Feedback & Suggestions
    Route::post('/feedback/submit', [SupportController::class, 'submitFeedback']);

    // Vitals & Labs APIs
    Route::get('/vitals-labs', [HealthRecordApiController::class, 'getHealthData']);
    Route::post('/vitals-labs', [HealthRecordApiController::class, 'saveHealthData']);

    // App Notifications
    Route::get('/notifications', [AppNotificationController::class, 'index']);
    Route::post('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [AppNotificationController::class, 'markAsRead']);

    // Subscriptions & Payment Verification
    Route::get('/subscription-plans/get', [SubscriptionPlanApiController::class, 'getPlans']);
    Route::get('/plan/get', [SubscriptionPlanApiController::class, 'getPlans']);
    Route::get('/subscription-features/get', [SubscriptionFeatureApiController::class, 'getFeatures']);
    Route::get('/permissions/get', [SubscriptionFeatureApiController::class, 'getFeatures']);
    Route::post('/subscription/save-transaction', [SubscriptionApiController::class, 'saveTransaction']);
    Route::post('/verify-payment', [SubscriptionApiController::class, 'verifyPayment']);
    Route::get('/user/subscription-status', [SubscriptionApiController::class, 'getSubscriptionStatus']);
    Route::get('/user/payment-history', [SubscriptionApiController::class, 'getPaymentHistory']);
});
