@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Create New Quote</h1>
                    <a href="{{ route('admin.quotes.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Back to Quotes
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.quotes.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('customer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Email *</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('customer_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone</label>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('customer_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Quote Details -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Quote Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Price</label>
                                    <input type="number" name="estimated_price" value="{{ old('estimated_price') }}" step="0.01" min="0"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('estimated_price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until *</label>
                                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" required
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('valid_until')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Designer</label>
                                    <select name="assigned_designer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Designer</option>
                                        @foreach($designers as $designer)
                                            <option value="{{ $designer->id }}" {{ old('assigned_designer_id') == $designer->id ? 'selected' : '' }}>
                                                {{ $designer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_designer_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Description -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Project Description *</h2>
                        <div>
                            <textarea name="project_description" rows="6" required
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Describe the project requirements...">{{ old('project_description') }}</textarea>
                            @error('project_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Requirements</h2>
                        <div>
                            <textarea name="requirements" rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="List specific requirements, specifications, or constraints...">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Admin Notes</h2>
                        <div>
                            <textarea name="admin_notes" rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Add internal notes about this quote...">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg">
                            Create Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
