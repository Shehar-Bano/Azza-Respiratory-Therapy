
<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SocialAuthController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
