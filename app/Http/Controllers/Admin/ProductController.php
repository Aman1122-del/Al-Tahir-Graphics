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

class ProductController extends Controller
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

        $services = $query->orderBy('sort_order')
                         ->orderByDesc('id')
                         ->paginate(20)
                         ->appends($request->query());

        // Get categories for filter dropdown
        $categories = Service::whereNotNull('category')
                            ->where('category', '!=', '')
                            ->distinct()
                            ->pluck('category')
                            ->sort();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);

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

        $service = Service::create($data);

        $this->syncSamples($service, $request->input('samples', []), $request);

        return redirect()->route('admin.services.index')->with('status', 'Service created');
    }

    public function edit(Service $service)
    {
        $service->load('samples');
        return view('admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($service->gallery_images) {
                foreach ($service->gallery_images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $service->update($data);

        $this->syncSamples($service, $request->input('samples', []), $request);

        return redirect()->route('admin.services.index')->with('status', 'Service updated');
    }

    public function destroy(Service $service)
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();
        return redirect()->route('admin.services.index')->with('status', 'Service deleted');
    }

    private function syncSamples(Service $service, array $samples, Request $request): void
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
                $sample = ServiceSample::where('service_id', $service->id)->where('id', $sampleData['id'])->first();
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

            $payload['service_id'] = $service->id;
            $created = ServiceSample::create($payload);
            $existingIds[] = $created->id;
        }

        // Delete removed samples
        if (!empty($existingIds)) {
            ServiceSample::where('service_id', $service->id)
                ->whereNotIn('id', $existingIds)
                ->delete();
        } else {
            ServiceSample::where('service_id', $service->id)->delete();
        }
    }
}


