<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FarmerController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes (SRS §1.6)
|--------------------------------------------------------------------------
| Protected by 'auth' and 'role:admin' middleware.
*/

// Admin Direct Login Route (Convenience fallback)
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard & Visual Overview
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Farmer Stall Management & Approval Gate (SRS §1.6)
    Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
    Route::get('/farmers/{id}', [FarmerController::class, 'show'])->name('farmers.show');
    Route::post('/farmers/{id}/approve', [FarmerController::class, 'approve'])->name('farmers.approve');
    Route::post('/farmers/{id}/suspend', [FarmerController::class, 'suspend'])->name('farmers.suspend');
    Route::post('/farmers/{id}/reinstate', [FarmerController::class, 'reinstate'])->name('farmers.reinstate');
    Route::delete('/farmers/{id}', [FarmerController::class, 'destroy'])->name('farmers.destroy');

    // 3. Customer Account Management (SRS §1.6)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // 4. Market Management (CRUD)
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
    Route::get('/markets/create', [MarketController::class, 'create'])->name('markets.create');
    Route::post('/markets', [MarketController::class, 'store'])->name('markets.store');
    Route::get('/markets/{id}/edit', [MarketController::class, 'edit'])->name('markets.edit');
    Route::put('/markets/{id}', [MarketController::class, 'update'])->name('markets.update');
    Route::delete('/markets/{id}', [MarketController::class, 'destroy'])->name('markets.destroy');

    // 5. Master Data / Product Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 6. Content Moderation
    Route::get('/moderation/reviews', [ModerationController::class, 'reviews'])->name('moderation.reviews');
    Route::delete('/moderation/reviews/{id}', [ModerationController::class, 'deleteReview'])->name('moderation.reviews.delete');
    Route::get('/moderation/products', [ModerationController::class, 'products'])->name('moderation.products');
    Route::post('/moderation/products/{id}/toggle', [ModerationController::class, 'toggleProduct'])->name('moderation.products.toggle');
    Route::delete('/moderation/products/{id}', [ModerationController::class, 'deleteProduct'])->name('moderation.products.delete');

    // 7. Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // 8. Platform Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::post('/announcements/{id}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // 9. System Settings & Platform Configurations
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/quick-toggle', [SettingController::class, 'quickToggle'])->name('settings.quick-toggle');

    // 10. Farmer Sales & Homepage Spotlight Moderation
    Route::get('/sales', [\App\Http\Controllers\Admin\SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales/{id}/feature', [\App\Http\Controllers\Admin\SaleController::class, 'feature'])->name('sales.feature');
    Route::post('/sales/{id}/unfeature', [\App\Http\Controllers\Admin\SaleController::class, 'unfeature'])->name('sales.unfeature');
    Route::post('/sales/{id}/toggle', [\App\Http\Controllers\Admin\SaleController::class, 'toggle'])->name('sales.toggle');
    Route::delete('/sales/{id}', [\App\Http\Controllers\Admin\SaleController::class, 'destroy'])->name('sales.destroy');

    // 11. Customer Complaints Moderation (Confidential to Admin)
    Route::get('/complaints', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{id}', [\App\Http\Controllers\Admin\ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{id}/status', [\App\Http\Controllers\Admin\ComplaintController::class, 'updateStatus'])->name('complaints.status');
    Route::delete('/complaints/{id}', [\App\Http\Controllers\Admin\ComplaintController::class, 'destroy'])->name('complaints.destroy');
});
