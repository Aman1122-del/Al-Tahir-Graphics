<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Quote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Dashboard statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'monthly_revenue' => Order::where('payment_status', 'paid')
                                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                    ->sum('total_amount'),
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_users' => User::count(),
            'pending_quotes' => Quote::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'assignedDesigner'])
                            ->latest()
                            ->take(10)
                            ->get();

        // Recent quotes
        $recentQuotes = Quote::with(['user', 'assignedDesigner'])
                            ->latest()
                            ->take(5)
                            ->get();

        // Monthly revenue chart data
        $monthlyRevenue = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as revenue')
                              ->where('payment_status', 'paid')
                              ->whereYear('created_at', date('Y'))
                              ->groupBy('month')
                              ->orderBy('month')
                              ->get()
                              ->pluck('revenue', 'month')
                              ->toArray();

        // Top selling services
        $topServices = OrderItem::selectRaw('service_id, SUM(quantity) as total_quantity')
                               ->with('service')
                               ->groupBy('service_id')
                               ->orderByDesc('total_quantity')
                               ->take(5)
                               ->get();

        $services = Service::orderBy('sort_order')->get(['id','title','slug','is_active']);
        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentQuotes', 'monthlyRevenue', 'topServices', 'services'));
    }

    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $analytics = [
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'revenue' => Order::where('payment_status', 'paid')
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->sum('total_amount'),
            'customers' => Order::whereBetween('created_at', [$startDate, $endDate])
                               ->distinct('user_id')
                               ->count('user_id'),
            'avg_order_value' => Order::where('payment_status', 'paid')
                                     ->whereBetween('created_at', [$startDate, $endDate])
                                     ->avg('total_amount'),
        ];

        return response()->json($analytics);
    }
}
