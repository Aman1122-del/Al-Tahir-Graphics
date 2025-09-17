@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="section-title">{{ $service->title }}</h1>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn-primary">Edit</a>
            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Service Details -->
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Service Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->slug }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->category ?: 'Uncategorized' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->sku ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Base Price</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->formatted_price }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price Display</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->price_display ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Featured</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $service->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Description</h2>
                <div class="prose max-w-none">
                    {!! nl2br(e($service->description)) !!}
                </div>
            </div>

            <!-- Gallery Images -->
            @if($service->gallery_images && count($service->gallery_images) > 0)
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Gallery Images</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($service->gallery_images as $image)
                    <div class="aspect-square rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($image) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Service Samples -->
            @if($service->samples->count() > 0)
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Service Samples</h2>
                <div class="space-y-4">
                    @foreach($service->samples as $sample)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $sample->title }}</h3>
                                @if($sample->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $sample->description }}</p>
                                @endif
                                <div class="flex items-center space-x-4 mt-2">
                                    @if($sample->unit_price)
                                    <span class="text-sm text-gray-500">Unit Price: {{ number_format($sample->unit_price, 2) }}</span>
                                    @endif
                                    @if($sample->price_display)
                                    <span class="text-sm text-gray-500">Display: {{ $sample->price_display }}</span>
                                    @endif
                                    <span class="text-sm text-gray-500">Type: {{ ucfirst($sample->sample_type) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($sample->image_path)
                                <img src="{{ Storage::url($sample->image_path) }}" alt="{{ $sample->title }}" class="w-12 h-12 rounded object-cover">
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sample->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sample->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Main Image -->
            @if($service->image_path)
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Main Image</h2>
                <img src="{{ Storage::url($service->image_path) }}" alt="{{ $service->title }}" class="w-full rounded-lg">
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <form action="{{ route('admin.services.toggle', $service) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                            {{ $service->is_active ? 'Deactivate' : 'Activate' }} Service
                        </button>
                    </form>
                    <a href="{{ route('admin.services.samples.create', $service) }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        Add Sample
                    </a>
                    <a href="{{ route('admin.services.samples.index', $service) }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        Manage Samples
                    </a>
                </div>
            </div>

            <!-- SEO Information -->
            <div class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6">
                <h2 class="text-xl font-semibold mb-4">SEO Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->meta_title ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->meta_description ?: 'Not set' }}</p>
                    </div>
                    @if($service->meta_keywords)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                        <p class="mt-1 text-sm text-gray-900">{{ is_array($service->meta_keywords) ? implode(', ', $service->meta_keywords) : $service->meta_keywords }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
