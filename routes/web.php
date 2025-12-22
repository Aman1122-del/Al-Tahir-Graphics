<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('service.show');
Route::get('/services/{service}/categories/{category}', [ServiceController::class, 'showCategory'])->name('service.category');
Route::get('/services/{service}/samples/{sample}', [ServiceController::class, 'showSample'])->name('service.sample');

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
Route::view('/about', 'pages.about-us')->name('about');

use App\Http\Controllers\ContactController;

// Page dikhane k liye
Route::get('/contact', [ContactController::class, 'index'])->name('contact');


// Form submit karne k liye (ye wo route hai jo missing tha)
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

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
Route::prefix('api/design')->group(function () {
    Route::post('/save', [DesignController::class, 'save'])->name('design.save');
    Route::get('/load/{design}', [DesignController::class, 'load'])->name('design.load');
    Route::post('/export', [DesignController::class, 'export'])->name('design.export');
});

// Temporary test route for debugging chat
Route::get('/test-chat', function () {
    $user = Auth::user();
    if (!$user) {
        return 'Not logged in';
    }

    $admin = App\Models\User::where('is_admin', true)->first();
    if (!$admin) {
        return 'No admin found';
    }

    $messages = App\Models\Message::where(function($q) use ($user, $admin) {
        $q->where('sender_id', $user->id)->where('receiver_id', $admin->id);
    })->orWhere(function($q) use ($user, $admin) {
        $q->where('sender_id', $admin->id)->where('receiver_id', $user->id);
    })->with(['sender:id,name', 'receiver:id,name'])->orderBy('created_at', 'asc')->get();

    return response()->json([
        'user' => $user,
        'admin' => $admin,
        'messages_count' => $messages->count(),
        'messages' => $messages
    ]);
})->middleware('auth');

// Test route for sending messages
Route::post('/test-send-message', function (Illuminate\Http\Request $request) {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Not logged in'], 401);
    }

    $admin = App\Models\User::where('is_admin', true)->first();
    if (!$admin) {
        return response()->json(['error' => 'No admin found'], 404);
    }

    try {
        $message = App\Models\Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $admin->id,
            'message' => $request->input('message', 'Test message'),
        ]);

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => $message,
            'debug' => [
                'user_id' => $user->id,
                'admin_id' => $admin->id,
                'user_is_admin' => $user->is_admin,
                'admin_is_admin' => $admin->is_admin
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Database error: ' . $e->getMessage()
        ], 500);
    }
})->middleware('auth');
