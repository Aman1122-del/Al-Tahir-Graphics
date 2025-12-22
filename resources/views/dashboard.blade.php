@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="section-title">Dashboard</h1>
            <p class="section-subtitle">Welcome back! Here's an overview of your account.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Quick Stats -->
            <div class="card text-center">
                <div class="text-3xl font-bold text-[--color-brand-deepblue] mb-2">
                    {{ auth()->user()->orders()->count() }}
                </div>
                <div class="text-sm font-medium text-slate-600">Total Orders</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-[--color-brand-deepblue] mb-2">
                    {{ auth()->user()->unreadMessages()->count() }}
                </div>
                <div class="text-sm font-medium text-slate-600">Unread Messages</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-[--color-brand-deepblue] mb-2">
                    {{ auth()->user()->cartItems()->count() }}
                </div>
                <div class="text-sm font-medium text-slate-600">Cart Items</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-[--color-brand-deepblue] mb-2">
                    {{ auth()->user()->orders()->where('order_status', 'completed')->count() }}
                </div>
                <div class="text-sm font-medium text-slate-600">Completed Orders</div>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <!-- Recent Orders -->
            <div class="card">
                <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Recent Orders</h3>
                @php
                    $recentOrders = auth()->user()->orders()->latest()->take(5)->get();
                @endphp

                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-b-0">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="#" class="text-sm text-[--color-brand-blue] hover:text-[--color-brand-orange] font-medium">
                            View All Orders →
                        </a>
                    </div>
                @else
                    <p class="text-slate-500 text-center py-8">No orders yet. Start shopping!</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('services') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-[--color-brand-blue] hover:bg-blue-50 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">Browse Services</p>
                            <p class="text-sm text-slate-500">Explore our design services</p>
                        </div>
                    </a>

                    <a href="{{ route('chat.index') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-[--color-brand-blue] hover:bg-blue-50 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">Chat Support</p>
                            <p class="text-sm text-slate-500">Get help from our team</p>
                        </div>
                    </a>

                    <a href="{{ route('returns.index') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-[--color-brand-blue] hover:bg-blue-50 transition-colors">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">My Returns</p>
                            <p class="text-sm text-slate-500">Manage return requests</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
