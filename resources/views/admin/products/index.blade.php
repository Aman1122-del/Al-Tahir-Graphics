@extends('layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6 pt-3">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
                <p class="text-gray-600 mt-1">Full AJAX CRUD for products & services with real-time updates</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Add New Product
            </a>
        </div>
        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6 mb-6">
            <form method="GET" class="grid gap-4 md:grid-cols-4">
                <div>
                    <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                        class="form-input w-full">
                </div>
                <div>
                    <select name="category" class="form-input w-full">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" class="form-input w-full">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="{{ route('admin.products.index') }}" class="btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Samples</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50" data-product-id="{{ $product->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if ($product->image_path)
                                            <img src="{{ $product->image_path }}" alt="{{ $product->title }}"
                                                class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $product->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $product->slug }}</p>
                                            @if ($product->is_featured)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Featured</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $product->category ?: 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $product->price_display ?: $product->formatted_price }}
                                    </div>
                                    @if ($product->price_display && $product->price)
                                        <div class="text-xs text-gray-500">Base: {{ $product->formatted_price }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $product->samples_count ?? $product->samples->count() }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if ($product->samples->where('is_active', true)->count() > 0)
                                            {{ $product->samples->where('is_active', true)->count() }} active
                                        @else
                                            No samples
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('service.show', $product) }}"
                                            class="text-blue-600 hover:text-blue-900" target="_blank" title="View on site">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <button class="toggle-featured-btn text-yellow-600 hover:text-yellow-900"
                                            data-product-id="{{ $product->id }}"
                                            data-featured="{{ $product->is_featured ? 'true' : 'false' }}"
                                            title="{{ $product->is_featured ? 'Remove from featured' : 'Add to featured' }}">
                                            <svg class="w-4 h-4"
                                                fill="{{ $product->is_featured ? 'currentColor' : 'none' }}"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button class="toggle-status-btn text-yellow-600 hover:text-yellow-900"
                                            data-product-id="{{ $product->id }}"
                                            data-status="{{ $product->is_active ? 'true' : 'false' }}">
                                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button class="delete-product-btn text-red-600 hover:text-red-900"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->title }}">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="space-y-3">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m14 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m14 0H6m0 0l4-4m0 0l4 4m-4-4v12">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium">No products found</p>
                                        <p>Get started by creating your first product.</p>
                                        <a href="{{ route('admin.products.create') }}" class="btn-primary">Create
                                            Product</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/products-management.js') }}"></script>
    @endpush
@endsection
