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
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // 4. Stall Profile & Geolocation Pinning
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 5. Customer Reviews & Responses
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');
});
