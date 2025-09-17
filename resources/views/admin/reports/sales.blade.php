@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Sales Report</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.reports.export-sales', request()->query()) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            Export CSV
                        </a>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to Reports
                        </a>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="flex items-end space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                                   class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                                   class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Total Revenue</h3>
                        <p class="text-3xl font-bold text-blue-600">PKR {{ number_format($totalRevenue, 0) }}</p>
                    </div>
                    
                    <div class="bg-green-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Total Orders</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $totalOrders }}</p>
                    </div>
                    
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Average Order Value</h3>
                        <p class="text-3xl font-bold text-purple-600">PKR {{ number_format($avgOrderValue, 0) }}</p>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h2 class="text-xl font-semibold mb-4">Daily Sales Trend</h2>
                    <div class="h-64 flex items-end justify-between space-x-2">
                        @php
                            $maxRevenue = $salesData->max('total_revenue') ?: 1;
                        @endphp
                        @foreach($salesData as $day)
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-full bg-indigo-500 rounded-t" 
                                     style="height: {{ ($day->total_revenue / $maxRevenue) * 200 }}px;">
                                </div>
                                <div class="text-xs text-gray-600 mt-2 text-center">
                                    {{ \Carbon\Carbon::parse($day->date)->format('M d') }}
                                </div>
                                <div class="text-xs font-semibold text-gray-800">
                                    PKR {{ number_format($day->total_revenue, 0) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sales Data Table -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Daily Sales Data</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesData as $day)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $day->orders_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        PKR {{ number_format($day->total_revenue, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        PKR {{ number_format($day->avg_order_value, 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No sales data found for the selected period.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
