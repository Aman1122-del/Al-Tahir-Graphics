<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceSampleController extends Controller
{
    /**
     * Display a listing of all service samples.
     */
    public function index()
    {
        $samples = ServiceSample::with('service')
            ->orderByDesc('id')
            ->paginate(20);
            
        return view('admin.samples.index', compact('samples'));
    }

    /**
     * Show the form for creating a new sample for a specific service.
     */
    public function create(Service $service)
    {
        return view('admin.samples.create', compact('service'));
    }

    /**
     * Store a newly created sample in storage.
     */
    public function store(Request $request, Service $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:service_samples,slug',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'price_display' => 'nullable|string|max:255',
            'sample_type' => 'nullable|string|max:100',
            'sub_category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['service_id'] = $service->id;
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('samples', 'public');
        }

        ServiceSample::create($data);

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('status', 'Sample created successfully');
    }

    /**
     * Show the form for editing the specified sample.
     */
    public function edit(ServiceSample $sample)
    {
        $service = $sample->service;
        return view('admin.samples.edit', compact('sample', 'service'));
    }

    /**
     * Update the specified sample in storage.
     */
    public function update(Request $request, ServiceSample $sample)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:service_samples,slug,' . $sample->id,
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'price_display' => 'nullable|string|max:255',
            'sample_type' => 'nullable|string|max:100',
            'sub_category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? $sample->sort_order;

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $sample->id);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($sample->image_path) {
                Storage::disk('public')->delete($sample->image_path);
            }
            $data['image_path'] = $request->file('image')->store('samples', 'public');
        }

        $sample->update($data);

        return redirect()
            ->route('admin.services.edit', $sample->service)
            ->with('status', 'Sample updated successfully');
    }

    /**
     * Remove the specified sample from storage.
     */
    public function destroy(ServiceSample $sample)
    {
        $serviceId = $sample->service_id;
        
        // Delete image if exists
        if ($sample->image_path) {
            Storage::disk('public')->delete($sample->image_path);
        }
        
        $sample->delete();

        return redirect()
            ->route('admin.services.edit', $serviceId)
            ->with('status', 'Sample deleted successfully');
    }

    /**
     * Generate a unique slug for the sample.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'sample';
        $slug = $base;
        $suffix = 2;
        
        while (ServiceSample::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }
        
        return $slug;
    }
}