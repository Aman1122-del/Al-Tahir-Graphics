@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Designers Report</h1>
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

                <!-- Designers Performance -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Designers Performance</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Designer</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Completed Orders</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Pending Orders</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Total Quotes</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Completion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($designerStats as $designer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.users.show', $designer) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $designer->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $designer->total_orders }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="text-green-600 font-semibold">{{ $designer->completed_orders }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="text-yellow-600 font-semibold">{{ $designer->pending_orders }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $designer->total_quotes }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $completionRate = $designer->total_orders > 0 ? ($designer->completed_orders / $designer->total_orders) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium">{{ number_format($completionRate, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No designers found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Stats -->
                @if($designerStats->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Total Designers</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $designerStats->count() }}</p>
                    </div>
                    
                    <div class="bg-green-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Total Orders Assigned</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $designerStats->sum('total_orders') }}</p>
                    </div>
                    
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Average Completion Rate</h3>
                        @php
                            $avgCompletionRate = $designerStats->filter(function($d) { return $d->total_orders > 0; })
                                ->map(function($d) { return ($d->completed_orders / $d->total_orders) * 100; })
                                ->avg();
                        @endphp
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($avgCompletionRate ?: 0, 1) }}%</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
