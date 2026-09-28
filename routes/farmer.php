<?php

use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\OrderController;
use App\Http\Controllers\Farmer\ProductController;
use App\Http\Controllers\Farmer\ProfileController;
use App\Http\Controllers\Farmer\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Farmer / Vendor Portal Routes (SRS §1.6)
|--------------------------------------------------------------------------
| Protected by 'auth' and 'role:farmer' middleware.
*/

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    // 1. Dashboard & Insights
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');

    // 2. Weekly Stock & Products CRUD
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/toggle-sold-out', [ProductController::class, 'toggleSoldOut'])->name('products.toggle-sold-out');
    Route::post('/products/replenish-template', [ProductController::class, 'replenishFromTemplate'])->name('products.replenish');

    // 3. Pre-Order Queue Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // 4. Stall Profile & Geolocation Pinning
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 5. Stall Operational Settings & Policies
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/quick-toggle', [ProfileController::class, 'quickToggle'])->name('settings.quick-toggle');

    // 6. Customer Reviews & Responses
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');

    // 7. Customer ↔ Farmer Chat / Stall Messages
    Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'farmerIndex'])->name('messages.index');
    Route::post('/messages/start', [\App\Http\Controllers\ChatController::class, 'startFromFarmer'])->name('messages.start');
    Route::post('/messages/{conversation}/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/{conversation}/poll', [\App\Http\Controllers\ChatController::class, 'pollMessages'])->name('messages.poll');

    // 8. Stall Promotions & Sales Campaigns
    Route::get('/sales', [\App\Http\Controllers\Farmer\SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [\App\Http\Controllers\Farmer\SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [\App\Http\Controllers\Farmer\SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}/edit', [\App\Http\Controllers\Farmer\SaleController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{id}', [\App\Http\Controllers\Farmer\SaleController::class, 'update'])->name('sales.update');
    Route::post('/sales/{id}/toggle', [\App\Http\Controllers\Farmer\SaleController::class, 'toggle'])->name('sales.toggle');
    Route::delete('/sales/{id}', [\App\Http\Controllers\Farmer\SaleController::class, 'destroy'])->name('sales.destroy');
});
