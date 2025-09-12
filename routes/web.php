<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('service.show');

// Cart sync endpoint for merging localStorage -> server (optional explicit route)
Route::post('/cart/sync', function(\Illuminate\Http\Request $request) {
    // Accept an array of items and push them to server-side cart
    $items = $request->validate([
        'items' => 'required|array|max:100',
        'items.*.service_id' => 'required|exists:services,id',
        'items.*.service_sample_id' => 'nullable|exists:service_samples,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'nullable|numeric|min:0',
    ])['items'];

    $controller = app(CartController::class);
    foreach ($items as $it) {
        $req = new \Illuminate\Http\Request($it);
        $controller->addToCart($req);
    }
    return response()->json(['success' => true]);
})->name('cart.sync');
Route::view('/wedding-cards', 'pages.wedding-cards')->name('wedding.cards');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

// Design Studio route
Route::get('/design', function () {
    return view('pages.design');
})->name('design');

// API routes for services
Route::get('/api/services/featured', [ServiceController::class, 'featured']);
Route::get('/api/services/category/{category}', [ServiceController::class, 'byCategory']);

// Cart routes
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::put('/cart/update/{cartItem}', [CartController::class, 'updateCartItem'])->name('cart.update');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeCartItem'])->name('cart.remove');
Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('cart.summary');
Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'showConfirmation'])->name('checkout.confirmation');

// Dashboard route (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include route files
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/chat.php';

// Design API routes
use App\Http\Controllers\DesignController;
Route::prefix('api/design')->group(function () {
    Route::post('/save', [DesignController::class, 'save'])->name('design.save');
    Route::get('/load/{design}', [DesignController::class, 'load'])->name('design.load');
    Route::post('/export', [DesignController::class, 'export'])->name('design.export');
});