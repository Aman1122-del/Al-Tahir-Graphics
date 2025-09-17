@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-8">Reports & Analytics</h1>

                <!-- Report Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <a href="{{ route('admin.reports.sales') }}" class="bg-blue-100 hover:bg-blue-200 p-6 rounded-lg transition-colors">
                        <div class="text-3xl mb-2">📊</div>
                        <h3 class="text-lg font-semibold text-blue-800">Sales Report</h3>
                        <p class="text-sm text-blue-600">Revenue and sales analytics</p>
                    </a>
                    
                    <a href="{{ route('admin.reports.orders') }}" class="bg-green-100 hover:bg-green-200 p-6 rounded-lg transition-colors">
                        <div class="text-3xl mb-2">📦</div>
                        <h3 class="text-lg font-semibold text-green-800">Orders Report</h3>
                        <p class="text-sm text-green-600">Order statistics and trends</p>
                    </a>
                    
                    <a href="{{ route('admin.reports.services') }}" class="bg-purple-100 hover:bg-purple-200 p-6 rounded-lg transition-colors">
                        <div class="text-3xl mb-2">🛍️</div>
                        <h3 class="text-lg font-semibold text-purple-800">Services Report</h3>
                        <p class="text-sm text-purple-600">Product performance metrics</p>
                    </a>
                    
                    <a href="{{ route('admin.reports.designers') }}" class="bg-yellow-100 hover:bg-yellow-200 p-6 rounded-lg transition-colors">
                        <div class="text-3xl mb-2">👨‍🎨</div>
                        <h3 class="text-lg font-semibold text-yellow-800">Designers Report</h3>
                        <p class="text-sm text-yellow-600">Designer performance and workload</p>
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h2 class="text-xl font-semibold mb-4">Quick Overview</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_orders'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Orders</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">PKR {{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                            <div class="text-sm text-gray-600">Total Revenue</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_services'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Active Services</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_users'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Users</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="font-medium">New order received</p>
                                    <p class="text-sm text-gray-600">Order #ATG20241201001 - PKR 15,000</p>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">2 hours ago</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="font-medium">Quote approved</p>
                                    <p class="text-sm text-gray-600">Quote #QT20241201001 - Wedding Card Design</p>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">4 hours ago</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="font-medium">New service added</p>
                                    <p class="text-sm text-gray-600">Business Card Design - PKR 2,500</p>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
