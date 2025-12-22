<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReturnRequestController extends Controller
{
    /**
     * Display the return/cancellation form.
     */
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');
        $orderItemId = $request->query('order_item_id');

        $order = null;
        $orderItem = null;

        if ($orderId) {
            $order = Order::where('id', $orderId)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();
        }

        if ($orderItemId) {
            $orderItem = OrderItem::where('id', $orderItemId)
                                 ->whereHas('order', function($query) {
                                     $query->where('user_id', Auth::id());
                                 })
                                 ->with('order')
                                 ->firstOrFail();
            $order = $orderItem->order;
        }

        // Get user's orders that can be returned (completed orders within 30 days)
        $eligibleOrders = Order::where('user_id', Auth::id())
                              ->where('order_status', 'completed')
                              ->where('created_at', '>=', now()->subDays(30))
                              ->with('orderItems')
                              ->get();

        return view('returns.create', compact('order', 'orderItem', 'eligibleOrders'));
    }

    /**
     * Store a new return request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'type' => ['required', Rule::in(['return', 'cancellation'])],
            'reason' => ['required', Rule::in([
                'defective_product',
                'wrong_item',
                'not_as_described',
                'changed_mind',
                'late_delivery',
                'other'
            ])],
            'description' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify ownership
        $order = Order::where('id', $request->order_id)
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

        // Check if order item belongs to the order
        if ($request->order_item_id) {
            $orderItem = OrderItem::where('id', $request->order_item_id)
                                 ->where('order_id', $order->id)
                                 ->firstOrFail();
        }

        // Check if return request already exists
        $existingRequest = ReturnRequest::where('order_id', $order->id)
                                       ->when($request->order_item_id, function($query) use ($request) {
                                           return $query->where('order_item_id', $request->order_item_id);
                                       })
                                       ->whereIn('status', ['pending', 'approved'])
                                       ->first();

        if ($existingRequest) {
            return back()->withErrors(['error' => 'A return request already exists for this order/item.']);
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('return-requests', 'public');
                $imagePaths[] = $path;
            }
        }

        // Create return request
        ReturnRequest::create([
            'order_id' => $order->id,
            'order_item_id' => $request->order_item_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'reason' => $request->reason,
            'description' => $request->description,
            'images' => $imagePaths,
            'status' => 'pending',
        ]);

        return redirect()->route('returns.index')->with('success', 'Your return/cancellation request has been submitted successfully.');
    }

    /**
     * Display user's return requests.
     */
    public function index()
    {
        $returnRequests = ReturnRequest::where('user_id', Auth::id())
                                      ->with(['order', 'orderItem'])
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(10);

        return view('returns.index', compact('returnRequests'));
    }

    /**
     * Display a specific return request.
     */
    public function show(ReturnRequest $returnRequest)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your return requests.');
        }

        // Ensure user owns this request
        if ($returnRequest->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this return request. It may not belong to you or you may be logged in as a different user.');
        }

        $returnRequest->load(['order.orderItems', 'orderItem']);

        return view('returns.show', compact('returnRequest'));
    }

    /**
     * Admin: List all return requests.
     */
    public function adminIndex()
    {
        $returnRequests = ReturnRequest::with(['order', 'orderItem', 'user'])
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(20);

        return view('admin.returns.index', compact('returnRequests'));
    }

    /**
     * Admin: Show return request details.
     */
    public function adminShow(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['order.orderItems', 'orderItem', 'user']);

        return view('admin.returns.show', compact('returnRequest'));
    }

    /**
     * Admin: Update return request status.
     */
    public function adminUpdate(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'completed', 'refunded'])],
            'admin_notes' => 'nullable|string|max:1000',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'status',
            'admin_notes',
            'refund_amount',
            'refund_method',
            'tracking_number',
        ]);

        if ($request->status === 'approved' && !$returnRequest->approved_at) {
            $data['approved_at'] = now();
        }

        if ($request->status === 'completed' && !$returnRequest->completed_at) {
            $data['completed_at'] = now();
        }

        $returnRequest->update($data);

        return redirect()->back()->with('success', 'Return request updated successfully.');
    }
}
