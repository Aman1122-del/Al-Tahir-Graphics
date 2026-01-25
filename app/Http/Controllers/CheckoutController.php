<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    /**
     * Display the checkout form.
     */
    public function showCheckout()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        $cartTotal = $cartItems->sum('total_price');
        $shippingCost = $this->calculateShippingCost($cartTotal);
        $totalAmount = $cartTotal + $shippingCost;

        return view('checkout.index', compact('cartItems', 'cartTotal', 'shippingCost', 'totalAmount'));
    }

    /**
     * Process the checkout and create the order.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|in:standard,express',
            'payment_method' => 'required|in:pay_on_delivery,manual_transfer',
            'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            $cartTotal = $cartItems->sum('total_price');
            $shippingCost = $this->calculateShippingCost($cartTotal);
            $totalAmount = $cartTotal + $shippingCost;

            // Handle payment screenshot upload
            $paymentScreenshotPath = null;
            if ($request->hasFile('payment_screenshot')) {
                $paymentScreenshotPath = $request->file('payment_screenshot')->store('payment-screenshots', 'public');
            }

            // Create the order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'subtotal' => $cartTotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'manual_transfer' ? 'pending_verification' : 'pending',
                'payment_screenshot_path' => $paymentScreenshotPath,
                'order_status' => 'pending',
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $cartItem->service_id,
                    'service_name' => $cartItem->service->title,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->total_price,
                    'custom_requirements' => $cartItem->custom_requirements,
                    'wedding_details' => $cartItem->wedding_details,
                    'visiting_card_details' => $cartItem->visiting_card_details,
                    'panaflex_details' => $cartItem->panaflex_details,
                    'flyer_brochure_details' => $cartItem->flyer_brochure_details,
                ]);
            }

            // Clear the cart
            $this->clearCart();

            DB::commit();

            // TODO: Send order confirmation email to customer
            // TODO: Send notification to admin about new order
            // TODO: If manual transfer, send account details to customer via WhatsApp/email

            return redirect()->route('checkout.confirmation', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if order creation fails
            if ($paymentScreenshotPath) {
                Storage::disk('public')->delete($paymentScreenshotPath);
            }

            return back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    /**
     * Display order confirmation page.
     */
    public function showConfirmation(Order $order)
    {
        // Ensure user can only view their own orders (unless admin)
        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Get cart items for the current user/session.
     */
    private function getCartItems()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        return CartItem::with('service')
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
     * Calculate shipping cost based on cart total and method.
     */
    private function calculateShippingCost(float $cartTotal): float
    {
        // Free shipping for orders over PKR 10,000
        if ($cartTotal >= 10000) {
            return 0;
        }

        // Standard shipping: PKR 500, Express: PKR 1000
        // This will be overridden by the shipping method selection
        return 500;
    }

    /**
     * Clear the cart after successful order creation.
     */
    private function clearCart(): void
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
    }
}
