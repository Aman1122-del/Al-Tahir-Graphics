@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="section-title">Edit Sample: {{ $sample->title }}</h1>
        <a href="{{ route('admin.services.edit', $service) }}" class="btn-secondary">Back to Service</a>
    </div>

    <form action="{{ route('admin.samples.update', $sample) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow ring-1 ring-black/5 p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Title *</label>
                <input name="title" value="{{ old('title', $sample->title) }}" class="form-input @error('title') border-red-500 @enderror" required />
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input name="slug" value="{{ old('slug', $sample->slug) }}" class="form-input @error('slug') border-red-500 @enderror" />
                <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Unit Price (PKR) *</label>
                <input name="unit_price" type="number" step="0.01" value="{{ old('unit_price', $sample->unit_price) }}" class="form-input @error('unit_price') border-red-500 @enderror" required />
                @error('unit_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Price Display</label>
                <input name="price_display" value="{{ old('price_display', $sample->price_display) }}" class="form-input @error('price_display') border-red-500 @enderror" placeholder="e.g., Starting from PKR 500" />
                <p class="text-xs text-gray-500 mt-1">Leave empty to use unit price</p>
                @error('price_display')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Sample Type</label>
                <select name="sample_type" class="form-input @error('sample_type') border-red-500 @enderror">
                    <option value="standard" {{ old('sample_type', $sample->sample_type) == 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="premium" {{ old('sample_type', $sample->sample_type) == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="deluxe" {{ old('sample_type', $sample->sample_type) == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                    <option value="custom" {{ old('sample_type', $sample->sample_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
                @error('sample_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Sub-Category *</label>
            <input name="sub_category" value="{{ old('sub_category', $sample->sub_category) }}" class="form-input @error('sub_category') border-red-500 @enderror" required placeholder="e.g., Traditional, Modern, Luxury" />
            @error('sub_category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea @error('description') border-red-500 @enderror">{{ old('description', $sample->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="form-label">Sample Image</label>
            @if($sample->image_path)
                <div class="mb-3">
                    <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}" class="w-32 h-24 rounded-lg object-cover">
                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-input @error('image') border-red-500 @enderror" />
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. Recommended: 800x600px or larger, JPG/PNG format</p>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Sort Order</label>
                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $sample->sort_order) }}" class="form-input @error('sort_order') border-red-500 @enderror" />
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                @error('sort_order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center space-x-4 mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sample->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Sample</button>
        </div>
    </form>

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-2xl shadow ring-1 ring-black/5 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="flex space-x-4">
            <a href="{{ route('service.sample', [$service->slug, $sample->slug]) }}" target="_blank" class="btn-secondary text-sm">
                View Sample Page
            </a>
            <a href="{{ route('service.category', [$service->slug, Str::slug($sample->sub_category)]) }}" target="_blank" class="btn-secondary text-sm">
                View Category Page
            </a>
        </div>
    </div>
</div>
@endsection
