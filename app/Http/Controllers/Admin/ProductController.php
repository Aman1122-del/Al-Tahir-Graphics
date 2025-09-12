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
    public function index()
    {
        $services = Service::orderByDesc('id')->paginate(20);
        return view('admin.services.index', compact('services'));
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
        // Expect up to 10 items: each with id(optional), title, price(optional), is_active, sort_order, image(optional)
        $existingIds = [];
        foreach (array_slice($samples, 0, 10) as $index => $sampleData) {
            $payload = [
                'title' => $sampleData['title'] ?? ('Sample ' . ($index+1)),
                'price' => $sampleData['price'] ?? null,
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


