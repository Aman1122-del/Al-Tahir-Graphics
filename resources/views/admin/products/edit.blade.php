@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Edit Product</h1>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6 ajax-form" data-action="edit" data-product-id="{{ $product->id }}">
        @csrf
        @method('PUT')
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Title *</label>
                <input name="title" class="form-input" value="{{ $product->title }}" required />
            </div>
            <div>
                <label class="form-label">Slug (optional)</label>
                <input name="slug" class="form-input" value="{{ $product->slug }}" />
            </div>
            <div>
                <label class="form-label">Base Price *</label>
                <input name="price" type="number" step="0.01" class="form-input" value="{{ $product->price }}" required />
            </div>
            <div>
                <label class="form-label">Price Display (optional)</label>
                <input name="price_display" class="form-input" value="{{ $product->price_display }}" placeholder="e.g., Starting from PKR 500" />
            </div>
            <div>
                <label class="form-label">Category</label>
                <input name="category" class="form-input" value="{{ $product->category }}" />
            </div>
            <div>
                <label class="form-label">Sort Order</label>
                <input name="sort_order" type="number" class="form-input" value="{{ $product->sort_order }}" />
            </div>
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea">{{ $product->description }}</textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Main Image</label>
                <input type="file" name="image" accept="image/*" class="form-input" />
                @if($product->image_path)
                    <div class="mt-2">
                        <img src="{{ $product->image_path }}" class="w-24 h-24 object-cover rounded" alt="Current image">
                        <p class="text-sm text-gray-500 mt-1">Current main image</p>
                    </div>
                @endif
            </div>
            <div>
                <label class="form-label">Image URL (fallback)</label>
                <input name="image_url" class="form-input" value="{{ $product->image_url }}" placeholder="https://example.com/image.jpg" />
            </div>
            <div>
                <label class="form-label">Gallery Images (up to 10)</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="form-input" />
                @if($product->gallery_images && count($product->gallery_images) > 0)
                    <div class="mt-2 grid grid-cols-4 gap-2">
                        @foreach($product->gallery_images as $image)
                            <img src="{{ asset('storage/' . $image) }}" class="w-16 h-16 object-cover rounded" alt="Gallery image">
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Current gallery images ({{ count($product->gallery_images) }})</p>
                @endif
            </div>
        </div>

        <!-- SEO Section -->
        <div class="rounded-xl bg-blue-50 p-4 ring-1 ring-blue-200">
            <h3 class="font-semibold mb-3 text-blue-900">SEO Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Meta Title</label>
                    <input name="meta_title" class="form-input" value="{{ $product->meta_title }}" maxlength="255" />
                </div>
                <div>
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-textarea" maxlength="500">{{ $product->meta_description }}</textarea>
                </div>
                <div>
                    <label class="form-label">Meta Keywords</label>
                    <textarea name="meta_keywords" rows="2" class="form-textarea" maxlength="1000" placeholder="Separate keywords with commas">{{ is_array($product->meta_keywords) ? implode(', ', $product->meta_keywords) : $product->meta_keywords }}</textarea>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <label class="inline-flex items-center"><input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="mr-2"> Active</label>
            <label class="inline-flex items-center"><input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="mr-2"> Featured</label>
        </div>

        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-black/5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Product Samples</h3>
                <a href="{{ route('admin.services.samples.create', $product) }}" class="btn-primary">Add New Sample</a>
            </div>
            
            @if($product->samples->count() > 0)
                <div class="space-y-3">
                    @foreach($product->samples->take(10) as $sample)
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($sample->image_path)
                                    <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}" class="w-16 h-16 rounded-lg object-cover">
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $sample->title }}</h4>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">{{ $sample->sample_type ?? 'standard' }}</span>
                                        @if($sample->sub_category) - {{ $sample->sub_category }}@endif
                                        - {{ $sample->formatted_price }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $sample->slug }}</p>
                                    @if($sample->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($sample->description, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $sample->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sample->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <a href="{{ route('admin.samples.edit', $sample) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                <form action="{{ route('admin.samples.destroy', $sample) }}" method="POST" class="inline" onsubmit="return confirm('Delete this sample?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($product->samples->count() > 10)
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.samples.index') }}?service={{ $product->id }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                            View all {{ $product->samples->count() }} samples
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No samples created yet.</p>
                    <a href="{{ route('admin.services.samples.create', $product) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Create your first sample</a>
                </div>
            @endif
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary" data-original-text="Save Changes">Save Changes</button>
        </div>
    </form>
</div>

@push('scripts')
<script src="{{ asset('js/products-management.js') }}"></script>
@endpush
@endsection
