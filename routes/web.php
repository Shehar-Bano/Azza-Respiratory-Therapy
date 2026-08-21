<?php

use App\Http\Controllers\Web\ArticleWebController;
use App\Http\Controllers\Web\CategoryWebController;
use App\Http\Controllers\Web\ClinicalCardWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SubscriptionPlanWebController;
use App\Http\Controllers\Web\UserSubscriptionWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Web Routes
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected Admin Dashboard Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/users', [UserWebController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserWebController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}/status', [UserWebController::class, 'updateStatus'])->name('admin.users.updateStatus');
    
    // Category Management
    Route::get('/admin/categories', [CategoryWebController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [CategoryWebController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [CategoryWebController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryWebController::class, 'destroy'])->name('admin.categories.destroy');

    // Article Management
    Route::get('/admin/articles', [ArticleWebController::class, 'index'])->name('admin.articles.index');
    Route::post('/admin/articles', [ArticleWebController::class, 'store'])->name('admin.articles.store');
    Route::put('/admin/articles/{article}', [ArticleWebController::class, 'update'])->name('admin.articles.update');
    Route::delete('/admin/articles/{article}', [ArticleWebController::class, 'destroy'])->name('admin.articles.destroy');
    Route::delete('/admin/articles/images/{image}', [ArticleWebController::class, 'destroyImage'])->name('admin.articles.images.destroy');

    // Clinical Cards Management
    Route::get('/admin/cards', [ClinicalCardWebController::class, 'index'])->name('admin.cards.index');
    Route::post('/admin/cards', [ClinicalCardWebController::class, 'store'])->name('admin.cards.store');
    Route::put('/admin/cards/{card}', [ClinicalCardWebController::class, 'update'])->name('admin.cards.update');
    Route::delete('/admin/cards/{card}', [ClinicalCardWebController::class, 'destroy'])->name('admin.cards.destroy');
    Route::delete('/admin/cards/images/{image}', [ClinicalCardWebController::class, 'destroyImage'])->name('admin.cards.images.destroy');

    Route::post('/admin/users/{user}/subscription', [UserWebController::class, 'updateSubscription'])->name('admin.users.updateSubscription');

    // Subscription Plans Management
    Route::get('/admin/plans', [SubscriptionPlanWebController::class, 'index'])->name('admin.plans.index');
    Route::put('/admin/plans/{plan}', [SubscriptionPlanWebController::class, 'update'])->name('admin.plans.update');

    // User Subscriptions Management
    Route::get('/admin/subscriptions', [UserSubscriptionWebController::class, 'index'])->name('admin.subscriptions.index');
    Route::post('/admin/subscriptions/check-expired', [UserSubscriptionWebController::class, 'checkExpired'])->name('admin.subscriptions.checkExpired');
    Route::put('/admin/subscriptions/{subscription}', [UserSubscriptionWebController::class, 'update'])->name('admin.subscriptions.update');
    Route::put('/admin/subscriptions/{subscription}/status', [UserSubscriptionWebController::class, 'updateStatus'])->name('admin.subscriptions.updateStatus');
});
