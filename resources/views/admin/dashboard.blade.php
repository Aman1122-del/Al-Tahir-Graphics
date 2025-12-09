@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Total Orders</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
                    </div>
                    
                    <div class="bg-green-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Total Revenue</h3>
                        <p class="text-3xl font-bold text-green-600">PKR {{ number_format($stats['total_revenue'], 0) }}</p>
                    </div>
                    
                    <div class="bg-yellow-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-800">Pending Orders</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
                    </div>
                    
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Active Services</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['active_services'] }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <a href="{{ route('admin.users.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center">
                        <h3 class="text-lg font-semibold">Manage Users</h3>
                        <p class="text-sm opacity-90">Add, edit, and manage user accounts</p>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center">
                        <h3 class="text-lg font-semibold">Manage Orders</h3>
                        <p class="text-sm opacity-90">View and process customer orders</p>
                    </a>
                    
                    <a href="{{ route('admin.products.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center">
                        <h3 class="text-lg font-semibold">Products Management</h3>
                        <p class="text-sm opacity-90">Full AJAX CRUD for products & services</p>
                    </a>
                    
                    <a href="{{ route('admin.reports.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center">
                        <h3 class="text-lg font-semibold">View Reports</h3>
                        <p class="text-sm opacity-90">Analytics and business insights</p>
                    </a>
                </div>

                <!-- Services Quick Toggle -->
                <div class="bg-white p-6 rounded-lg shadow mb-8">
                    <h2 class="text-xl font-semibold mb-4">Services</h2>
                    <div class="divide-y divide-gray-100">
                        @foreach($services as $svc)
                            <div class="flex items-center justify-between py-3">
                                <div>
                                    <div class="font-medium">{{ $svc->title }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $svc->id }}</div>
                                </div>
                                <button
                                    x-data="{ id: {{ $svc->id }}, active: {{ $svc->is_active ? 'true' : 'false' }} }"
                                    @click="
                                        fetch('{{ route('admin.services.toggle', ['service' => '__SERVICE_ID__']) }}'.replace('__SERVICE_ID__', id), {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                            }
                                        }).then(r=>r.json()).then(d=>{ if(d.success){ active = d.service.is_active }})
                                    "
                                    class="px-3 py-1 rounded text-white"
                                    :class="active ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-500 hover:bg-gray-600'"
                                >
                                    <span x-text="active ? 'Active' : 'Inactive'"></span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Order #</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Customer</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Amount</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Designer</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $order->customer_name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">PKR {{ number_format($order->total_amount, 0) }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($order->order_status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        {{ $order->assignedDesigner ? $order->assignedDesigner->name : 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Quotes -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Recent Quotes</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Quote #</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Customer</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Project</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Designer</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQuotes as $quote)
                                <tr>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <a href="{{ route('admin.quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $quote->quote_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $quote->customer_name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        {{ Str::limit($quote->project_description, 50) }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($quote->status === 'approved') bg-green-100 text-green-800
                                            @elseif($quote->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($quote->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        {{ $quote->assignedDesigner ? $quote->assignedDesigner->name : 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $quote->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
