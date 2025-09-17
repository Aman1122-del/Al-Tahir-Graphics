@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">User Details - {{ $user->name }}</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                            Edit User
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to Users
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- User Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">User Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Roles</label>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($role->name === 'admin') bg-red-100 text-red-800
                                                @elseif($role->name === 'designer') bg-blue-100 text-blue-800
                                                @elseif($role->name === 'support') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Verified</span>
                                        @else
                                            <span class="text-red-600">✗ Not Verified</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- User Orders -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">User Orders ({{ $user->orders->count() }})</h2>
                            @if($user->orders->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->orders->take(10) as $order)
                                            <tr>
                                                <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                    PKR {{ number_format($order->total_amount, 0) }}
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($order->order_status === 'completed') bg-green-100 text-green-800
                                                        @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                                        @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($order->order_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-500">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($user->orders->count() > 10)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('admin.orders.index', ['search' => $user->email]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View all {{ $user->orders->count() }} orders
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-500">No orders found for this user.</p>
                            @endif
                        </div>

                        <!-- Assigned Orders (for designers) -->
                        @if($user->assignedOrders->count() > 0)
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Assigned Orders ({{ $user->assignedOrders->count() }})</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Design Status</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->assignedOrders->take(10) as $order)
                                        <tr>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                {{ $order->customer_name }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($order->design_status === 'completed') bg-green-100 text-green-800
                                                    @elseif($order->design_status === 'in_progress') bg-blue-100 text-blue-800
                                                    @elseif($order->design_status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($order->design_status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-500">
                                                {{ $order->design_due_date ? $order->design_due_date->format('M d, Y') : 'Not set' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Quick Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                            <div class="space-y-3">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md block text-center">
                                    Edit User
                                </a>
                                
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                            Delete User
                                        </button>
                                    </form>
                                @endif
                                
                                @if($user->id !== auth()->id() && !$user->isAdmin())
                                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                            Impersonate User
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- User Stats -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">User Statistics</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Orders:</span>
                                    <span class="text-sm font-medium">{{ $user->orders->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Assigned Orders:</span>
                                    <span class="text-sm font-medium">{{ $user->assignedOrders->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Quotes:</span>
                                    <span class="text-sm font-medium">{{ $user->quotes->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Assigned Quotes:</span>
                                    <span class="text-sm font-medium">{{ $user->assignedQuotes->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
