@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Quotes Management</h1>
                    <a href="{{ route('admin.quotes.create') }}" 
                       class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
                        Create Quote
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Quote #, Customer, Email" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designer</label>
                            <select name="designer" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Designers</option>
                                @foreach($designers as $designer)
                                    <option value="{{ $designer->id }}" {{ request('designer') == $designer->id ? 'selected' : '' }}>
                                        {{ $designer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quotes Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quote #
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Project
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Designer
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Valid Until
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quotes as $quote)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.quotes.show', $quote) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $quote->quote_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ $quote->customer_name }}</div>
                                        <div class="text-gray-500">{{ $quote->customer_email }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $quote->project_description }}">
                                        {{ Str::limit($quote->project_description, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($quote->estimated_price)
                                        PKR {{ number_format($quote->estimated_price, 0) }}
                                    @else
                                        <span class="text-gray-400">TBD</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($quote->status === 'approved') bg-green-100 text-green-800
                                        @elseif($quote->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($quote->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($quote->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quote->assignedDesigner ? $quote->assignedDesigner->name : 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $quote->valid_until->format('M d, Y') }}
                                    @if($quote->isExpired())
                                        <span class="text-red-500 text-xs">(Expired)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.quotes.show', $quote) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('admin.quotes.edit', $quote) }}" 
                                           class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        @if($quote->status === 'pending')
                                            <form method="POST" action="{{ route('admin.quotes.approve', $quote) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.quotes.reject', $quote) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No quotes found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $quotes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
