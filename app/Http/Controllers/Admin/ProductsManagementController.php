<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('samples');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $products = $query->orderBy('sort_order')
                         ->orderByDesc('id')
                         ->paginate(20)
                         ->appends($request->query());

        // Get categories for filter dropdown
        $categories = Service::whereNotNull('category')
                            ->where('category', '!=', '')
                            ->distinct()
                            ->pluck('category')
                            ->sort();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = null;
        }

        // Convert meta_keywords string to array
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_filter(array_map('trim', explode(',', $data['meta_keywords'])));
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $product = Service::create($data);
        $this->syncSamples($product, $request->input('samples', []), $request);

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully');
    }

    public function edit(Service $product)
    {
        $product->load('samples');
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateServiceRequest $request, Service $product)
    {
        $data = $request->validated();
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = null;
        }

        // Convert meta_keywords string to array
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_filter(array_map('trim', explode(',', $data['meta_keywords'])));
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $product->update($data);
        $this->syncSamples($product, $request->input('samples', []), $request);

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully');
    }

    public function destroy(Service $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully');
    }

    // AJAX endpoints for real-time updates
    public function storeAjax(StoreServiceRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = null;
        }

        // Convert meta_keywords string to array
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_filter(array_map('trim', explode(',', $data['meta_keywords'])));
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $product = Service::create($data);
        $this->syncSamples($product, $request->input('samples', []), $request);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product->load('samples')
        ]);
    }

    public function updateAjax(UpdateServiceRequest $request, Service $product)
    {
        $data = $request->validated();
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = null;
        }

        // Convert meta_keywords string to array
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_filter(array_map('trim', explode(',', $data['meta_keywords'])));
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $product->update($data);
        $this->syncSamples($product, $request->input('samples', []), $request);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product->load('samples')
        ]);
    }

    public function destroyAjax(Service $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $productId = $product->id;
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'product_id' => $productId
        ]);
    }

    public function toggleStatus(Service $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Product status updated',
            'product' => $product
        ]);
    }

    public function toggleFeatured(Service $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        
        return response()->json([
            'success' => true,
            'message' => 'Product featured status updated',
            'product' => $product
        ]);
    }

    private function syncSamples(Service $product, array $samples, Request $request): void
    {
        // Expect up to 10 items: each with id(optional), title, unit_price(optional), price_display, sample_type, etc.
        $existingIds = [];
        foreach (array_slice($samples, 0, 10) as $index => $sampleData) {
            $payload = [
                'title' => $sampleData['title'] ?? ('Sample ' . ($index+1)),
                'unit_price' => $sampleData['unit_price'] ?? null,
                'price_display' => $sampleData['price_display'] ?? null,
                'sample_type' => $sampleData['sample_type'] ?? 'standard',
                'sub_category' => $sampleData['sub_category'] ?? null,
                'description' => $sampleData['description'] ?? null,
                'is_active' => isset($sampleData['is_active']) ? (bool)$sampleData['is_active'] : true,
                'sort_order' => $sampleData['sort_order'] ?? $index,
            ];

            if (isset($sampleData['id'])) {
                $sample = ServiceSample::where('service_id', $product->id)->where('id', $sampleData['id'])->first();
                if ($sample) {
                    if ($request->hasFile("samples.$index.image")) {
                        if ($sample->image_path) { Storage::disk('public')->delete($sample->image_path); }
                        $payload['image_path'] = $request->file("samples.$index.image")->store('products', 'public');
                    }
                    $sample->update($payload);
                    $existingIds[] = $sample->id;
                    continue;
                }
            }

            if ($request->hasFile("samples.$index.image")) {
                $payload['image_path'] = $request->file("samples.$index.image")->store('products', 'public');
            }

            $payload['service_id'] = $product->id;
            $created = ServiceSample::create($payload);
            $existingIds[] = $created->id;
        }

        // Delete removed samples
        if (!empty($existingIds)) {
            ServiceSample::where('service_id', $product->id)
                ->whereNotIn('id', $existingIds)
                ->delete();
        } else {
            ServiceSample::where('service_id', $product->id)->delete();
        }
    }
}
