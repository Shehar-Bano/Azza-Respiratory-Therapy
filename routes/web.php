<?php

use App\Http\Controllers\Web\ArticleWebController;
use App\Http\Controllers\Web\ClinicalCardWebController;
use App\Http\Controllers\Web\DashboardController;
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
    
    // Article Management
    Route::get('/admin/articles', [ArticleWebController::class, 'index'])->name('admin.articles.index');
    Route::post('/admin/articles', [ArticleWebController::class, 'store'])->name('admin.articles.store');
    Route::put('/admin/articles/{article}', [ArticleWebController::class, 'update'])->name('admin.articles.update');
    Route::delete('/admin/articles/{article}', [ArticleWebController::class, 'destroy'])->name('admin.articles.destroy');

    // Clinical Cards Management
    Route::get('/admin/cards', [ClinicalCardWebController::class, 'index'])->name('admin.cards.index');
    Route::post('/admin/cards', [ClinicalCardWebController::class, 'store'])->name('admin.cards.store');
    Route::put('/admin/cards/{card}', [ClinicalCardWebController::class, 'update'])->name('admin.cards.update');
    Route::delete('/admin/cards/{card}', [ClinicalCardWebController::class, 'destroy'])->name('admin.cards.destroy');
});
