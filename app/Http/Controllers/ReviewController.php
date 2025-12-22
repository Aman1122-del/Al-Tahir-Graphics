<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Order $order): View
    {
        // Check if user owns the order and it's completed
        if ($order->user_id !== auth()->id() || $order->order_status !== 'completed') {
            abort(403);
        }

        // Check if user already reviewed this order
        $existingReview = Review::where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('dashboard')->with('error', 'You have already reviewed this order.');
        }

        return view('reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        // Check if user owns the order and it's completed
        if ($order->user_id !== auth()->id() || $order->order_status !== 'completed') {
            abort(403);
        }

        // Check if user already reviewed this order
        $existingReview = Review::where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('dashboard')->with('error', 'You have already reviewed this order.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Create reviews for each service in the order
        foreach ($order->orderItems as $item) {
            Review::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'service_id' => $item->service_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => false, // Admin approval required
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Thank you for your review! It will be published after approval.');
    }
}
