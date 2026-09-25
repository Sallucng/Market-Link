<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes (SRS §1.6)
|--------------------------------------------------------------------------
| Protected by 'auth' and 'role:admin' middleware.
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard & User Approvals
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/farmers/{id}/approve', [DashboardController::class, 'approveFarmer'])->name('farmers.approve');
    Route::post('/farmers/{id}/suspend', [DashboardController::class, 'suspendFarmer'])->name('farmers.suspend');
    Route::post('/customers/{id}/toggle-status', [DashboardController::class, 'toggleCustomerStatus'])->name('customers.toggle');

    // 2. Market Management (CRUD)
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
    Route::get('/markets/create', [MarketController::class, 'create'])->name('markets.create');
    Route::post('/markets', [MarketController::class, 'store'])->name('markets.store');
    Route::get('/markets/{id}/edit', [MarketController::class, 'edit'])->name('markets.edit');
    Route::put('/markets/{id}', [MarketController::class, 'update'])->name('markets.update');
    Route::delete('/markets/{id}', [MarketController::class, 'destroy'])->name('markets.destroy');

    // 3. Master Data / Product Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 4. Content Moderation
    Route::get('/moderation/reviews', [ModerationController::class, 'reviews'])->name('moderation.reviews');
    Route::delete('/moderation/reviews/{id}', [ModerationController::class, 'deleteReview'])->name('moderation.reviews.delete');
    Route::get('/moderation/products', [ModerationController::class, 'products'])->name('moderation.products');
    Route::post('/moderation/products/{id}/toggle', [ModerationController::class, 'toggleProduct'])->name('moderation.products.toggle');
    Route::delete('/moderation/products/{id}', [ModerationController::class, 'deleteProduct'])->name('moderation.products.delete');

    // 5. Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // 6. Platform Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::post('/announcements/{id}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});
