<?php

use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminCustomerManagementController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminFarmerManagementController;
use App\Http\Controllers\Api\Admin\AdminMarketController;
use App\Http\Controllers\Api\Admin\AdminModerationController;
use App\Http\Controllers\Api\Admin\AdminNotificationController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Farmer\FarmerAuthController;
use App\Http\Controllers\Api\Farmer\FarmerDashboardController;
use App\Http\Controllers\Api\Farmer\FarmerOrderController;
use App\Http\Controllers\Api\Farmer\FarmerProductController;
use App\Http\Controllers\Api\Farmer\FarmerProfileController;
use App\Http\Controllers\Api\Farmer\FarmerReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MarketBrowseController;
use App\Http\Controllers\ProductBrowseController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/farmer', [FarmerAuthController::class, 'register']);
Route::post('/farmer/register', [FarmerAuthController::class, 'register']);
Route::post('/farmer/login', [FarmerAuthController::class, 'login']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/v1/farmer/login', [FarmerAuthController::class, 'login']);
Route::post('/v1/admin/login', [AdminAuthController::class, 'login']);

// Authenticated Logout (Any Role)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Public Markets & Farmers
Route::get('/markets', [MarketBrowseController::class, 'index']);
Route::get('/v1/markets', [MarketBrowseController::class, 'index']);
Route::get('/markets/{id}', [MarketBrowseController::class, 'show']);
Route::get('/v1/markets/{id}', [MarketBrowseController::class, 'show']);
Route::get('/farmers/{farmerId}', [MarketBrowseController::class, 'showFarmer']);
Route::get('/v1/farmers/{farmerId}', [MarketBrowseController::class, 'showFarmer']);

// Public Categories & Products
Route::get('/categories', [ProductBrowseController::class, 'categories']);
Route::get('/v1/categories', [ProductBrowseController::class, 'categories']);
Route::get('/products', [ProductBrowseController::class, 'index']);
Route::get('/v1/products', [ProductBrowseController::class, 'index']);
Route::get('/products/{id}', [ProductBrowseController::class, 'show']);
Route::get('/v1/products/{id}', [ProductBrowseController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    // Customer Profile
    Route::get('/customer/profile', [CustomerProfileController::class, 'show']);
    Route::put('/customer/profile', [CustomerProfileController::class, 'update']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index']);
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show']);
    Route::post('/checkout', [CustomerOrderController::class, 'checkout']);
    Route::post('/orders/{id}/cancel', [CustomerOrderController::class, 'cancel']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Protected Farmer Routes (V1 & Legacy Compatibility)
|--------------------------------------------------------------------------
*/

$farmerRoutes = function () {
    // Farmer Dashboard & Insights
    Route::get('/dashboard', [FarmerDashboardController::class, 'statistics']);
    Route::get('/dashboard/stats', [FarmerDashboardController::class, 'statistics']);
    Route::get('/dashboard/statistics', [FarmerDashboardController::class, 'statistics']);
    Route::get('/statistics', [FarmerDashboardController::class, 'statistics']);

    // Farmer Profile & Markets
    Route::get('/profile', [FarmerProfileController::class, 'show']);
    Route::put('/profile', [FarmerProfileController::class, 'update']);
    Route::get('/markets', [FarmerProfileController::class, 'markets']);
    Route::post('/markets/join', [FarmerProfileController::class, 'joinMarket']);
    Route::delete('/markets/{marketId}/leave', [FarmerProfileController::class, 'leaveMarket']);
    Route::post('/markets/leave', [FarmerProfileController::class, 'leaveMarket']);

    // Farmer Products
    Route::get('/products', [FarmerProductController::class, 'index']);
    Route::post('/products', [FarmerProductController::class, 'store']);
    Route::get('/products/{id}', [FarmerProductController::class, 'show']);
    Route::put('/products/{id}', [FarmerProductController::class, 'update']);
    Route::delete('/products/{id}', [FarmerProductController::class, 'destroy']);
    Route::match(['patch', 'post', 'put'], '/products/{id}/status', [FarmerProductController::class, 'toggleAvailability']);
    Route::match(['patch', 'post', 'put'], '/products/{id}/toggle-availability', [FarmerProductController::class, 'toggleAvailability']);
    Route::post('/products/{id}/weekly-stock', [FarmerProductController::class, 'updateWeeklyStock']);

    // Farmer Orders
    Route::get('/orders', [FarmerOrderController::class, 'index']);
    Route::get('/orders/{id}', [FarmerOrderController::class, 'show']);
    Route::match(['patch', 'put', 'post'], '/orders/{id}/status', [FarmerOrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/accept', [FarmerOrderController::class, 'accept']);
    Route::post('/orders/{id}/ready-for-pickup', [FarmerOrderController::class, 'markReadyForPickup']);
    Route::post('/orders/{id}/complete', [FarmerOrderController::class, 'complete']);
    Route::post('/orders/{id}/decline', [FarmerOrderController::class, 'decline']);

    // Farmer Statistics
    Route::get('/statistics', [FarmerOrderController::class, 'statistics']);

    // Farmer Reviews
    Route::get('/reviews', [FarmerReviewController::class, 'index']);
    Route::post('/reviews/{id}/reply', [FarmerReviewController::class, 'reply']);
};

Route::middleware(['auth:sanctum', 'role:farmer'])->prefix('v1/farmer')->group($farmerRoutes);
Route::middleware(['auth:sanctum', 'role:farmer'])->prefix('farmer')->group($farmerRoutes);

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (V1 & Legacy Compatibility)
|--------------------------------------------------------------------------
*/

$adminRoutes = function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'index']);

    // Admin Users
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);

    // Admin Categories
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

    // Admin Markets (CRUD)
    Route::get('/markets', [AdminMarketController::class, 'index']);
    Route::post('/markets', [AdminMarketController::class, 'store']);
    Route::get('/markets/{id}', [AdminMarketController::class, 'show']);
    Route::put('/markets/{id}', [AdminMarketController::class, 'update']);
    Route::delete('/markets/{id}', [AdminMarketController::class, 'destroy']);

    // Announcements
    Route::get('/announcements', [AdminAnnouncementController::class, 'index']);
    Route::post('/announcements', [AdminAnnouncementController::class, 'store']);
    Route::get('/announcements/{id}', [AdminAnnouncementController::class, 'show']);
    Route::put('/announcements/{id}', [AdminAnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AdminAnnouncementController::class, 'destroy']);

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index']);
    Route::post('/reports', [AdminReportController::class, 'store']);
    Route::get('/reports/{id}', [AdminReportController::class, 'show']);
    Route::put('/reports/{id}', [AdminReportController::class, 'update']);
    Route::delete('/reports/{id}', [AdminReportController::class, 'destroy']);

    // Farmer Management
    Route::get('/farmers', [AdminFarmerManagementController::class, 'index']);
    Route::get('/farmers/{id}', [AdminFarmerManagementController::class, 'show']);
    Route::match(['patch', 'post'], '/farmers/{id}/approve', [AdminFarmerManagementController::class, 'approve']);
    Route::match(['patch', 'post'], '/farmers/{id}/suspend', [AdminFarmerManagementController::class, 'suspend']);
    Route::match(['patch', 'post'], '/farmers/{id}/reject', [AdminFarmerManagementController::class, 'reject']);

    // Customer Management
    Route::get('/customers', [AdminCustomerManagementController::class, 'index']);
    Route::get('/customers/{id}', [AdminCustomerManagementController::class, 'show']);
    Route::put('/customers/{id}', [AdminCustomerManagementController::class, 'update']);
    Route::match(['put', 'post'], '/customers/{id}/status', [AdminCustomerManagementController::class, 'toggleStatus']);

    // Product Management
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    // Moderation (Product & Review)
    Route::get('/moderation/products', [AdminModerationController::class, 'products']);
    Route::match(['patch', 'post', 'delete'], '/moderation/products/{id}', [AdminModerationController::class, 'moderateProduct']);
    Route::match(['patch', 'post', 'delete'], '/products/{id}/moderate', [AdminModerationController::class, 'moderateProduct']);
    Route::get('/moderation/reviews', [AdminModerationController::class, 'reviews']);
    Route::match(['patch', 'post', 'delete'], '/moderation/reviews/{id}', [AdminModerationController::class, 'moderateReview']);
    Route::match(['patch', 'post', 'delete'], '/reviews/{id}/moderate', [AdminModerationController::class, 'moderateReview']);

    // Notification Management
    Route::get('/notifications', [AdminNotificationController::class, 'index']);
    Route::post('/notifications', [AdminNotificationController::class, 'store']);
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy']);
};

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('v1/admin')->group($adminRoutes);
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group($adminRoutes);