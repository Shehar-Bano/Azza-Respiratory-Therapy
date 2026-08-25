
<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\SubscriptionFeatureApiController;
use App\Http\Controllers\Api\SubscriptionPlanApiController;
use App\Http\Controllers\Api\V1\AppNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/fcm-token', [AppNotificationController::class, 'updateFcmToken']);

    // App Notifications
    Route::get('/notifications', [AppNotificationController::class, 'index']);
    Route::post('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [AppNotificationController::class, 'markAsRead']);

    Route::get('/article/get', [ArticleApiController::class, 'getArticles']);
    Route::get('/card/get', [CardApiController::class, 'getCards']);
    Route::get('/category/get', [CategoryApiController::class, 'getCategories']);
    Route::get('/category/artical/get', [ArticleApiController::class, 'getCategoryArticles']);
    Route::get('/subscription-plans/get', [SubscriptionPlanApiController::class, 'getPlans']);
    Route::get('/plan/get', [SubscriptionPlanApiController::class, 'getPlans']);
    Route::get('/subscription-features/get', [SubscriptionFeatureApiController::class, 'getFeatures']);
    Route::get('/permissions/get', [SubscriptionFeatureApiController::class, 'getFeatures']);
    Route::post('/subscription/save-transaction', [SubscriptionApiController::class, 'saveTransaction']);
    Route::get('/user/subscription-status', [SubscriptionApiController::class, 'getSubscriptionStatus']);
    Route::get('/user/payment-history', [SubscriptionApiController::class, 'getPaymentHistory']);

});
