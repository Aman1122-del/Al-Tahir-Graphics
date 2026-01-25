@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Order Details - {{ $order->order_number }}</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.orders.edit', $order) }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                            Edit Order
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to Orders
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Order Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Order Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Order Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($order->order_status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Design Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($order->design_status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->design_status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($order->design_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->design_status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Design Due Date</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $order->design_due_date ? $order->design_due_date->format('M d, Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->customer_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->customer_phone ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User Account</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($order->user)
                                            <a href="{{ route('admin.users.show', $order->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $order->user->name }}
                                            </a>
                                        @else
                                            Guest Order
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Order Items</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                        <tr>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                {{ $item->service->title }}
                                                @if($item->wedding_details)
                                                    <div class="mt-2 p-2 bg-blue-50 rounded text-xs">
                                                        <strong>Wedding Details:</strong><br>
                                                        Groom: {{ $item->wedding_details['groom'] ?? 'N/A' }} | Bride: {{ $item->wedding_details['bride'] ?? 'N/A' }}<br>
                                                        @if(isset($item->wedding_details['eventType']) && !empty($item->wedding_details['eventType']))
                                                            Event: {{ $item->wedding_details['eventType'] }} |
                                                        @endif
                                                        @if(isset($item->wedding_details['dateTime']) && !empty($item->wedding_details['dateTime']))
                                                            Date/Time: {{ $item->wedding_details['dateTime'] }}<br>
                                                        @endif
                                                        @if(isset($item->wedding_details['venue']) && !empty($item->wedding_details['venue']))
                                                            Venue: {{ $item->wedding_details['venue'] }}<br>
                                                        @endif
                                                        @if(!empty($item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] ?? ''))
                                                            Message: {{ $item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($item->visiting_card_details)
                                                    <div class="mt-2 p-2 bg-green-50 rounded text-xs">
                                                        <strong>Visiting Card Details:</strong><br>
                                                        Name: {{ $item->visiting_card_details['businessName'] ?? 'N/A' }}<br>
                                                        @if(isset($item->visiting_card_details['designation']) && !empty($item->visiting_card_details['designation']))
                                                            Designation: {{ $item->visiting_card_details['designation'] }}<br>
                                                        @endif
                                                        @if(isset($item->visiting_card_details['companyName']) && !empty($item->visiting_card_details['companyName']))
                                                            Company: {{ $item->visiting_card_details['companyName'] }}<br>
                                                        @endif
                                                        Mobile: {{ $item->visiting_card_details['mobileNumber'] ?? 'N/A' }}<br>
                                                        @if(isset($item->visiting_card_details['whatsappNumber']) && !empty($item->visiting_card_details['whatsappNumber']))
                                                            WhatsApp: {{ $item->visiting_card_details['whatsappNumber'] }}<br>
                                                        @endif
                                                        @if(isset($item->visiting_card_details['emailAddress']) && !empty($item->visiting_card_details['emailAddress']))
                                                            Email: {{ $item->visiting_card_details['emailAddress'] }}<br>
                                                        @endif
                                                        Printing: {{ $item->visiting_card_details['printingSide'] ?? 'N/A' }}<br>
                                                        @if(isset($item->visiting_card_details['officeAddress']) && !empty($item->visiting_card_details['officeAddress']))
                                                            Address: {{ $item->visiting_card_details['officeAddress'] }}<br>
                                                        @endif
                                                        @if(!empty($item->visiting_card_details['additionalMessage'] ?? ''))
                                                            Message: {{ $item->visiting_card_details['additionalMessage'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($item->panaflex_details)
                                                    <div class="mt-2 p-2 bg-purple-50 rounded text-xs">
                                                        <strong>Panaflex Printing Details:</strong><br>
                                                        Business/Event Name: {{ $item->panaflex_details['businessName'] ?? 'N/A' }}<br>
                                                        Panaflex Size: {{ $item->panaflex_details['panaflexSize'] ?? 'N/A' }}<br>
                                                        @if(isset($item->panaflex_details['eventType']) && !empty($item->panaflex_details['eventType']))
                                                            Event Type: {{ $item->panaflex_details['eventType'] }}<br>
                                                        @endif
                                                        Main Heading: {{ $item->panaflex_details['mainHeading'] ?? 'N/A' }}<br>
                                                        @if(isset($item->panaflex_details['subHeading']) && !empty($item->panaflex_details['subHeading']))
                                                            Sub Heading: {{ $item->panaflex_details['subHeading'] }}<br>
                                                        @endif
                                                        @if(isset($item->panaflex_details['dateTime']) && !empty($item->panaflex_details['dateTime']))
                                                            Date/Time: {{ $item->panaflex_details['dateTime'] }}<br>
                                                        @endif
                                                        Venue/Location: {{ $item->panaflex_details['venue'] ?? 'N/A' }}<br>
                                                        Contact Number: {{ $item->panaflex_details['contactNumber'] ?? 'N/A' }}<br>
                                                        @if(!empty($item->panaflex_details['additionalInstructions'] ?? ''))
                                                            Instructions: {{ $item->panaflex_details['additionalInstructions'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($item->flyer_brochure_details)
                                                    <div class="mt-2 p-2 bg-emerald-50 rounded text-xs">
                                                        <strong>Flyer/Brochure Details:</strong><br>
                                                        Business/Brand Name: {{ $item->flyer_brochure_details['businessName'] ?? 'N/A' }}<br>
                                                        Brochure Type: {{ $item->flyer_brochure_details['brochureType'] ?? 'N/A' }}<br>
                                                        Paper Type: {{ $item->flyer_brochure_details['paperType'] ?? 'N/A' }}<br>
                                                        Fold Type: {{ $item->flyer_brochure_details['foldType'] ?? 'N/A' }}<br>
                                                        Quantity: {{ $item->flyer_brochure_details['quantity'] ?? 'N/A' }}<br>
                                                        Contact Info: {{ $item->flyer_brochure_details['contactInfo'] ?? 'N/A' }}<br>
                                                        @if(isset($item->flyer_brochure_details['validityDate']) && !empty($item->flyer_brochure_details['validityDate']))
                                                            Validity Date: {{ $item->flyer_brochure_details['validityDate'] }}<br>
                                                        @endif
                                                        @if(isset($item->flyer_brochure_details['address']) && !empty($item->flyer_brochure_details['address']))
                                                            Address: {{ $item->flyer_brochure_details['address'] }}<br>
                                                        @endif
                                                        @if(isset($item->flyer_brochure_details['offerDetails']) && !empty($item->flyer_brochure_details['offerDetails']))
                                                            Offer Details: {{ $item->flyer_brochure_details['offerDetails'] }}<br>
                                                        @endif
                                                        @if(!empty($item->flyer_brochure_details['additionalMessage'] ?? ''))
                                                            Message: {{ $item->flyer_brochure_details['additionalMessage'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                PKR {{ number_format($item->unit_price, 0) }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                PKR {{ number_format($item->total_price, 0) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                            <td class="px-4 py-2 font-semibold">PKR {{ number_format($order->subtotal, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Shipping:</td>
                                            <td class="px-4 py-2 font-semibold">PKR {{ number_format($order->shipping_cost, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                            <td class="px-4 py-2 font-semibold text-lg">PKR {{ number_format($order->total_amount, 0) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Order Notes</h2>
                            @if($order->orderNotes->count() > 0)
                                <div class="space-y-4">
                                    @foreach($order->orderNotes as $note)
                                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm text-gray-900">{{ $note->note }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $note->user->name }} - {{ $note->created_at->format('M d, Y H:i') }}
                                                    @if($note->is_internal)
                                                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Internal</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($note->type) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No notes available.</p>
                            @endif

                            <!-- Add Note Form -->
                            <form method="POST" action="{{ route('admin.orders.add-note', $order) }}" class="mt-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Add Note</label>
                                        <textarea name="note" rows="3"
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Add a note about this order..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Note Type</label>
                                        <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="admin">Admin</option>
                                            <option value="designer">Designer</option>
                                            <option value="system">System</option>
                                            <option value="customer">Customer</option>
                                        </select>
                                        <div class="mt-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-sm text-gray-600">Internal Note</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md">
                                        Add Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Designer Assignment -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Designer Assignment</h2>
                            @if($order->assignedDesigner)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Assigned Designer</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $order->assignedDesigner->name }}</p>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.orders.assign-designer', $order) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Designer</label>
                                        <select name="assigned_designer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Designer</option>
                                            @foreach($designers as $designer)
                                                <option value="{{ $designer->id }}" {{ $order->assigned_designer_id == $designer->id ? 'selected' : '' }}>
                                                    {{ $designer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                        <input type="date" name="design_due_date" value="{{ $order->design_due_date ? $order->design_due_date->format('Y-m-d') : '' }}"
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Design Notes</label>
                                        <textarea name="design_notes" rows="3"
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Add design notes...">{{ $order->design_notes }}</textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md">
                                        Assign Designer
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-sm font-medium">PKR {{ number_format($order->subtotal, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Shipping:</span>
                                    <span class="text-sm font-medium">PKR {{ number_format($order->shipping_cost, 0) }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-base font-semibold">Total:</span>
                                        <span class="text-base font-semibold">PKR {{ number_format($order->total_amount, 0) }}</span>
                                    </div>
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
