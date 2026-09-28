<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Public\AiAssistantController;
use App\Http\Controllers\Public\FarmerProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MarketController;
use App\Http\Controllers\Public\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront & Discovery Routes (Person 1)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/locale/{lang}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/nearby', [MarketController::class, 'nearby'])->name('markets.nearby');
Route::get('/markets/nearby/json', [MarketController::class, 'nearbyJson'])->name('markets.nearby.json');
Route::get('/markets/{id}', [MarketController::class, 'show'])->name('markets.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::get('/farmers', [FarmerProfileController::class, 'index'])->name('farmers.index');
Route::get('/farmers/{id}', [FarmerProfileController::class, 'show'])->name('farmers.show');

Route::get('/api/search/live', [ProductController::class, 'liveSearch'])->name('api.search.live');
Route::post('/api/ai-assistant', [AiAssistantController::class, 'query'])->name('ai.assistant');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Pre-Order Shopping Cart
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Customer Authenticated Portal (Person 1)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Checkout (Pay at pickup)
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

    // Customer Dashboard & Pre-Orders
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::post('/orders/{id}/modify', [OrderController::class, 'modify'])->name('orders.modify');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{id}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
        Route::post('/orders/{id}/review', [ReviewController::class, 'store'])->name('orders.review');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

        // Customer ↔ Farmer Chat / Stall Inquiries
        Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'customerIndex'])->name('messages.index');
        Route::post('/messages/start', [\App\Http\Controllers\ChatController::class, 'startFromCustomer'])->name('messages.start');
        Route::post('/messages/{conversation}/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('messages.send');
        Route::get('/messages/{conversation}/poll', [\App\Http\Controllers\ChatController::class, 'pollMessages'])->name('messages.poll');

        // Settings & Shopping Preferences
        Route::get('/settings', [CustomerDashboardController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [CustomerDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/quick-toggle', [CustomerDashboardController::class, 'quickToggle'])->name('settings.quick-toggle');

        // Confidential Farmer Complaints (Visible only to Customer & Admin)
        Route::get('/complaints', [\App\Http\Controllers\Customer\ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/create', [\App\Http\Controllers\Customer\ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/complaints', [\App\Http\Controllers\Customer\ComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/complaints/{id}', [\App\Http\Controllers\Customer\ComplaintController::class, 'show'])->name('complaints.show');
    });
});

/*
|--------------------------------------------------------------------------
| Interactive OpenAPI / REST Documentation Viewer
|--------------------------------------------------------------------------
*/
Route::get('/docs/openapi.yaml', function () {
    $path = base_path('docs/openapi.yaml');
    if (!file_exists($path)) {
        abort(404);
    }
    return response(file_get_contents($path), 200, [
        'Content-Type' => 'text/yaml; charset=UTF-8',
    ]);
});

Route::get('/docs', function () {
    return view('docs.index');
})->name('api.docs');

Route::get('/api/documentation', function () {
    return redirect()->route('api.docs');
});

/*
|--------------------------------------------------------------------------
| Farmer & Admin Portals (Person 2 Routes)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/farmer.php';
require __DIR__ . '/admin.php';
