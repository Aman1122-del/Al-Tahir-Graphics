@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Products
            </a>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="space-y-8 ajax-form" data-action="edit" data-product-id="{{ $product->id }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
                </div>
                <div class="p-6 grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                class="text-red-500">*</span></label>
                        <input name="title" type="text"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out form-input"
                            value="{{ $product->title }}" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span
                                class="text-gray-400 text-xs">(Auto-generated if empty)</span></label>
                        <input name="slug" type="text"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 form-input"
                            value="{{ $product->slug }}" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input name="category" type="text"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 form-input"
                            value="{{ $product->category }}" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base Price <span
                                class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                {{-- <span class="text-gray-500 sm:text-sm">PKR</span> --}}
                            </div>
                            <input name="price" type="number" step="0.01"
                                class="block w-full rounded-lg border-gray-300 pl-12 focus:border-indigo-500 focus:ring-indigo-500 form-input"
                                value="{{ $product->price }}" required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price Display Label</label>
                        <input name="price_display" type="text"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 form-input"
                            value="{{ $product->price_display }}" placeholder="e.g., Starting from PKR 500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input name="sort_order" type="number"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 form-input"
                            value="{{ $product->sort_order }}" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="5"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 form-textarea">{{ $product->description }}</textarea>
                    </div>

                    <div class="md:col-span-2 flex space-x-8 pt-2 border-t border-gray-100 mt-2">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1"
                                {{ $product->is_active ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer">
                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-indigo-600">Active
                                Status</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_featured" value="1"
                                {{ $product->is_featured ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer">
                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-indigo-600">Mark as
                                Featured</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Media & Images</h3>
                </div>
                <div class="p-6 grid gap-8 md:grid-cols-2">

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Main Product Image</label>
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <input type="file" name="image" accept="image/*"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 form-input" />
                                <div class="mt-2">
                                    <label class="text-xs text-gray-500 mb-1 block">Or use External URL:</label>
                                    <input name="image_url" class="w-full text-sm rounded-md border-gray-300 form-input"
                                        value="{{ $product->image_url }}" placeholder="https://example.com/image.jpg" />
                                </div>
                            </div>
                            @if ($product->image_path)
                                <div class="flex-shrink-0 border p-1 rounded bg-white shadow-sm">
                                    <img src="{{ $product->image_path }}" class="w-24 h-24 object-cover rounded"
                                        alt="Current image">
                                    <p class="text-[10px] text-center text-gray-400 mt-1 uppercase tracking-wider">Current
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Gallery Images (Max 10)</label>
                        <input type="file" name="gallery_images[]" accept="image/*" multiple
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 form-input" />

                        @if ($product->gallery_images && count($product->gallery_images) > 0)
                            <div class="mt-4">
                                <p class="text-xs text-gray-500 mb-2">Current Gallery
                                    ({{ count($product->gallery_images) }})</p>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                                    @foreach ($product->gallery_images as $image)
                                        <div class="relative group aspect-square">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                class="w-full h-full object-cover rounded border border-gray-200"
                                                alt="Gallery">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-blue-50 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Search Engine Optimization</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input name="meta_title" type="text" maxlength="255"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-input"
                            value="{{ $product->meta_title }}" />
                        <p class="text-xs text-gray-400 mt-1">Recommended length: 50-60 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="500"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-textarea">{{ $product->meta_description }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Recommended length: 150-160 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" maxlength="1000"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-textarea"
                            placeholder="keyword1, keyword2, keyword3">{{ is_array($product->meta_keywords) ? implode(', ', $product->meta_keywords) : $product->meta_keywords }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Product Samples</h3>
                    <a href="{{ route('admin.services.samples.create', $product) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        + Add New Sample
                    </a>
                </div>

                <div class="p-6">
                    @if ($product->samples->count() > 0)
                        <div class="space-y-4">
                            @foreach ($product->samples->take(10) as $sample)
                                <div
                                    class="bg-white rounded-lg p-4 border border-gray-200 hover:border-indigo-300 transition-colors duration-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-start space-x-4">
                                            @if ($sample->image_path)
                                                <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}"
                                                    class="w-16 h-16 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                            @else
                                                <div
                                                    class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                                    No Img</div>
                                            @endif
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $sample->title }}</h4>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $sample->sample_type ?? 'Standard' }}
                                                    </span>
                                                    @if ($sample->sub_category)
                                                        <span class="text-xs text-gray-500">•
                                                            {{ $sample->sub_category }}</span>
                                                    @endif
                                                    <span class="text-xs font-semibold text-indigo-600">•
                                                        {{ $sample->formatted_price }}</span>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1 font-mono">{{ $sample->slug }}</p>
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $sample->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $sample->is_active ? 'Active' : 'Inactive' }}
                                            </span>

                                            <div class="flex items-center border-l pl-4 ml-2 space-x-3">
                                                <a href="{{ route('admin.samples.edit', $sample) }}"
                                                    class="text-gray-500 hover:text-indigo-600 transition-colors"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.samples.destroy', $sample) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this sample?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-gray-400 hover:text-red-600 transition-colors pt-1"
                                                        title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($product->samples->count() > 10)
                            <div class="mt-6 text-center border-t pt-4">
                                <a href="{{ route('admin.samples.index') }}?service={{ $product->id }}"
                                    class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    View all {{ $product->samples->count() }} samples
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No samples created yet.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.services.samples.create', $product) }}"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium hover:underline">
                                    Create your first sample
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-medium transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm transition duration-150 ease-in-out"
                    data-original-text="Save Changes">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('js/products-management.js') }}"></script>
    @endpush
@endsection
