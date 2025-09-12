<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $salesData = Order::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as orders_count,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order_value
            ')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = Order::where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->sum('total_amount');

        $totalOrders = Order::where('payment_status', 'paid')
                           ->whereBetween('created_at', [$startDate, $endDate])
                           ->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('admin.reports.sales', compact('salesData', 'totalRevenue', 'totalOrders', 'avgOrderValue', 'startDate', 'endDate'));
    }

    public function orders(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $ordersData = Order::with(['user', 'assignedDesigner'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply filters
        if ($request->has('status')) {
            $ordersData->where('order_status', $request->status);
        }

        if ($request->has('payment_status')) {
            $ordersData->where('payment_status', $request->payment_status);
        }

        $orders = $ordersData->latest()->paginate(50);

        $statusCounts = Order::whereBetween('created_at', [$startDate, $endDate])
                            ->selectRaw('order_status, COUNT(*) as count')
                            ->groupBy('order_status')
                            ->pluck('count', 'order_status')
                            ->toArray();

        $paymentStatusCounts = Order::whereBetween('created_at', [$startDate, $endDate])
                                   ->selectRaw('payment_status, COUNT(*) as count')
                                   ->groupBy('payment_status')
                                   ->pluck('count', 'payment_status')
                                   ->toArray();

        return view('admin.reports.orders', compact('orders', 'statusCounts', 'paymentStatusCounts', 'startDate', 'endDate'));
    }

    public function services(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $topServices = OrderItem::selectRaw('
                service_id,
                services.title,
                SUM(quantity) as total_quantity,
                SUM(total_price) as total_revenue,
                COUNT(DISTINCT order_id) as orders_count
            ')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('service_id', 'services.title')
            ->orderByDesc('total_revenue')
            ->get();

        $serviceCategories = Service::selectRaw('
                category,
                COUNT(*) as total_services,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_services
            ')
            ->groupBy('category')
            ->get();

        return view('admin.reports.services', compact('topServices', 'serviceCategories', 'startDate', 'endDate'));
    }

    public function designers(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $designerStats = User::role('designer')
            ->withCount(['assignedOrders as total_orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['assignedQuotes as total_quotes' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($designer) use ($startDate, $endDate) {
                $designer->completed_orders = $designer->assignedOrders()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('design_status', 'completed')
                    ->count();

                $designer->pending_orders = $designer->assignedOrders()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('design_status', ['pending', 'in_progress'])
                    ->count();

                return $designer;
            });

        return view('admin.reports.designers', compact('designerStats', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $salesData = Order::selectRaw('
                orders.order_number,
                orders.customer_name,
                orders.customer_email,
                orders.total_amount,
                orders.payment_status,
                orders.order_status,
                orders.created_at,
                users.name as designer_name
            ')
            ->leftJoin('users', 'orders.assigned_designer_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->get();

        $filename = 'sales_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($salesData) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Order Number', 'Customer', 'Email', 'Total Amount',
                'Payment Status', 'Order Status', 'Designer', 'Created Date'
            ]);

            foreach ($salesData as $sale) {
                fputcsv($file, [
                    $sale->order_number,
                    $sale->customer_name,
                    $sale->customer_email,
                    $sale->total_amount,
                    $sale->payment_status,
                    $sale->order_status,
                    $sale->designer_name ?? 'Unassigned',
                    $sale->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
