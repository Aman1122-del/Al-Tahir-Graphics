<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function viewCart()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');

        return view('cart.view', compact('cartItems', 'cartTotal', 'itemCount'));
    }

    /**
     * Add a service to the cart.
     */
    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_sample_id' => 'nullable|exists:service_samples,id',
            'quantity' => 'required|integer|min:1',
            'custom_requirements' => 'nullable|string|max:500',
            'unit_price' => 'nullable|numeric|min:0',
            'design_id' => 'nullable|exists:designs,id',
            'wedding_details' => 'nullable|array',
            'visiting_card_details' => 'nullable|array',
            'panaflex_details' => 'nullable|array',
            'flyer_brochure_details' => 'nullable|array',
            // Accept either a URL or a base64 data URL for previews
            'design_preview_url' => ['nullable', function ($attribute, $value, $fail) {
                if ($value === null) return;
                if (is_string($value) && (str_starts_with($value, 'data:image/') || filter_var($value, FILTER_VALIDATE_URL))) {
                    return;
                }
                $fail('The '.$attribute.' must be a valid URL or base64 image data URL.');
            }],
        ]);

        $service = Service::findOrFail($request->service_id);
        $sessionId = Session::getId();
        $userId = Auth::id();

        // Check if item already exists in cart
        $existingItem = CartItem::where('service_id', $request->service_id)
            ->when($request->filled('service_sample_id'), function($q) use ($request) {
                $q->where('service_sample_id', $request->service_sample_id);
            }, function($q){
                $q->whereNull('service_sample_id');
            })
            // Treat different variants (by unit_price and custom_requirements) as separate items
            ->when($request->filled('unit_price'), function($q) use ($request) {
                $q->where('unit_price', $request->unit_price);
            }, function($q) use ($service) {
                $q->where('unit_price', $service->price);
            })
            ->when($request->filled('custom_requirements'), function($q) use ($request) {
                $q->where('custom_requirements', $request->custom_requirements);
            })
            ->when($request->filled('design_id'), function($q) use ($request) {
                $q->where('design_id', $request->design_id);
            })
            ->where(function ($query) use ($sessionId, $userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($existingItem) {
            // Update existing item
            $existingItem->update([
                'quantity' => $existingItem->quantity + $request->quantity,
                'custom_requirements' => $request->custom_requirements ?: $existingItem->custom_requirements,
                'wedding_details' => $request->wedding_details ?: $existingItem->wedding_details,
                'visiting_card_details' => $request->visiting_card_details ?: $existingItem->visiting_card_details,
                'panaflex_details' => $request->panaflex_details ?: $existingItem->panaflex_details,
                'flyer_brochure_details' => $request->flyer_brochure_details ?: $existingItem->flyer_brochure_details,
            ]);
        } else {
            // Create new cart item
            $created = CartItem::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
                'service_id' => $request->service_id,
                'service_sample_id' => $request->service_sample_id,
                'design_id' => $request->design_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->filled('unit_price') ? $request->unit_price : $service->price,
                'custom_requirements' => $request->custom_requirements,
                'design_preview_path' => $this->storePreviewIfProvided($request->design_preview_url),
                'wedding_details' => $request->wedding_details,
                'visiting_card_details' => $request->visiting_card_details,
                'panaflex_details' => $request->panaflex_details,
                'flyer_brochure_details' => $request->flyer_brochure_details,
            ]);
        }

        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Service added to cart successfully!',
            'cart_total' => 'PKR ' . number_format($cartTotal, 0),
            'item_count' => $itemCount,
        ]);
    }

    private function storePreviewIfProvided(?string $previewUrl): ?string
    {
        if (!$previewUrl) return null;
        // Already a storage URL
        $parsed = parse_url($previewUrl, PHP_URL_PATH);
        if ($parsed && str_starts_with($parsed, '/storage/')) {
            return ltrim(substr($parsed, strlen('/storage/')), '/');
        }

        // Handle base64 data URL like: data:image/png;base64,AAA...
        if (str_starts_with($previewUrl, 'data:image/')) {
            try {
                [$meta, $content] = explode(',', $previewUrl, 2);
                if (!isset($content)) return null;
                $mime = null;
                if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64$/', $meta, $m)) {
                    $mime = $m[1];
                }
                $extension = match($mime) {
                    'image/png' => 'png',
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/webp' => 'webp',
                    'image/svg+xml' => 'svg',
                    default => 'png'
                };
                $binary = base64_decode($content, true);
                if ($binary === false) return null;
                $path = 'design_previews/' . uniqid('preview_', true) . '.' . $extension;
                Storage::disk('public')->put($path, $binary);
                return $path;
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Any other http(s) URL that is not ours: do not fetch, ignore
        return null;
    }

    /**
     * Update a cart item.
     */
    public function updateCartItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'custom_requirements' => 'nullable|string|max:500',
        ]);

        // Ensure user can only update their own cart items
        if (!$this->canModifyCartItem($cartItem)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'custom_requirements' => $request->custom_requirements,
        ]);

        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully!',
            'cart_total' => 'PKR ' . number_format($cartTotal, 0),
            'item_count' => $itemCount,
            'item_total' => 'PKR ' . number_format($cartItem->total_price, 0),
        ]);
    }

    /**
     * Remove a cart item.
     */
    public function removeCartItem(CartItem $cartItem): JsonResponse
    {
        // Ensure user can only remove their own cart items
        if (!$this->canModifyCartItem($cartItem)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully!',
            'cart_total' => 'PKR ' . number_format($cartTotal, 0),
            'item_count' => $itemCount,
        ]);
    }

    /**
     * Get cart summary for AJAX requests.
     */
    public function getCartSummary(): JsonResponse
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');

        return response()->json([
            'cart_total' => 'PKR ' . number_format($cartTotal, 0),
            'item_count' => $itemCount,
        ]);
    }

    /**
     * Clear the entire cart.
     */
    public function clearCart(): JsonResponse
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        CartItem::where(function ($query) use ($sessionId, $userId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!',
            'cart_total' => 'PKR 0',
            'item_count' => 0,
        ]);
    }

    /**
     * Get cart items for the current user/session.
     */
    private function getCartItems()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        return CartItem::with(['service', 'sample'])
            ->where(function ($query) use ($sessionId, $userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();
    }

    /**
     * Check if the current user can modify a cart item.
     */
    private function canModifyCartItem(CartItem $cartItem): bool
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        if ($userId) {
            return $cartItem->user_id === $userId;
        }

        return $cartItem->session_id === $sessionId;
    }
}
