@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Services Report</h1>
                    <a href="{{ route('admin.reports.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Back to Reports
                    </a>
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

                <!-- Top Services -->
                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h2 class="text-xl font-semibold mb-4">Top Performing Services</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Quantity Sold</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServices as $service)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $service->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $service->total_quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        PKR {{ number_format($service->total_revenue, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $service->orders_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        PKR {{ number_format($service->total_revenue / $service->total_quantity, 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No service data found for the selected period.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Service Categories -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Service Categories Overview</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($serviceCategories as $category)
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-2">
                                {{ $category->category ?: 'Uncategorized' }}
                            </h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Services:</span>
                                    <span class="text-sm font-medium">{{ $category->total_services }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Active Services:</span>
                                    <span class="text-sm font-medium">{{ $category->active_services }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Inactive Services:</span>
                                    <span class="text-sm font-medium">{{ $category->total_services - $category->active_services }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center text-gray-500 py-8">
                            No category data available.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
