@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Quote Details - {{ $quote->quote_number }}</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.quotes.edit', $quote) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                            Edit Quote
                        </a>
                        <a href="{{ route('admin.quotes.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to Quotes
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quote Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Quote Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quote Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->quote_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($quote->status === 'approved') bg-green-100 text-green-800
                                        @elseif($quote->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($quote->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($quote->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estimated Price</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($quote->estimated_price)
                                            PKR {{ number_format($quote->estimated_price, 0) }}
                                        @else
                                            <span class="text-gray-400">To be determined</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Valid Until</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $quote->valid_until->format('M d, Y') }}
                                        @if($quote->isExpired())
                                            <span class="text-red-500 text-xs ml-1">(Expired)</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_phone ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User Account</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($quote->user)
                                            <a href="{{ route('admin.users.show', $quote->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $quote->user->name }}
                                            </a>
                                        @else
                                            Guest Quote
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Project Description -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Project Description</h2>
                            <div class="prose max-w-none">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $quote->project_description }}</p>
                            </div>
                        </div>

                        <!-- Requirements -->
                        @if($quote->requirements)
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Requirements</h2>
                            <div class="prose max-w-none">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $quote->requirements }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Admin Notes -->
                        @if($quote->admin_notes)
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Admin Notes</h2>
                            <div class="prose max-w-none">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $quote->admin_notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Designer Notes -->
                        @if($quote->designer_notes)
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Designer Notes</h2>
                            <div class="prose max-w-none">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $quote->designer_notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Designer Assignment -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Designer Assignment</h2>
                            @if($quote->assignedDesigner)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Assigned Designer</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $quote->assignedDesigner->name }}</p>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.quotes.assign-designer', $quote) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Designer</label>
                                        <select name="assigned_designer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Designer</option>
                                            @foreach($designers as $designer)
                                                <option value="{{ $designer->id }}" {{ $quote->assigned_designer_id == $designer->id ? 'selected' : '' }}>
                                                    {{ $designer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md">
                                        Assign Designer
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                            <div class="space-y-3">
                                @if($quote->status === 'pending')
                                    <form method="POST" action="{{ route('admin.quotes.approve', $quote) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                                            Approve Quote
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.quotes.reject', $quote) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                            Reject Quote
                                        </button>
                                    </form>
                                @endif
                                
                                @if($quote->status === 'approved')
                                    <div class="text-center text-green-600 font-medium">
                                        ✓ Quote Approved
                                    </div>
                                @endif
                                
                                @if($quote->status === 'rejected')
                                    <div class="text-center text-red-600 font-medium">
                                        ✗ Quote Rejected
                                    </div>
                                @endif
                                
                                @if($quote->isExpired())
                                    <div class="text-center text-gray-600 font-medium">
                                        ⏰ Quote Expired
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quote Summary -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Quote Summary</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ ucfirst($quote->status) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Estimated Price:</span>
                                    <span class="text-sm font-medium">
                                        @if($quote->estimated_price)
                                            PKR {{ number_format($quote->estimated_price, 0) }}
                                        @else
                                            TBD
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Valid Until:</span>
                                    <span class="text-sm font-medium">{{ $quote->valid_until->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <span class="text-sm font-medium">{{ $quote->created_at->format('M d, Y') }}</span>
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
