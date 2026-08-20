
<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\SubscriptionPlanApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);
Route::get('/article/get', [ArticleApiController::class, 'getArticles']);
Route::get('/card/get', [CardApiController::class, 'getCards']);
Route::get('/category/get', [CategoryApiController::class, 'getCategories']);
Route::get('/category/artical/get', [ArticleApiController::class, 'getCategoryArticles']);
Route::get('/subscription-plans/get', [SubscriptionPlanApiController::class, 'getPlans']);
Route::get('/plan/get', [SubscriptionPlanApiController::class, 'getPlans']);
Route::post('/subscription/save-transaction', [SubscriptionApiController::class, 'saveTransaction']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
