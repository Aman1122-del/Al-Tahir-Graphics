<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceSample;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of all active services.
     */
    public function index()
    {
        $services = Service::active()->ordered()->get();
        
        return view('pages.services', compact('services'));
    }

    /**
     * Display the specified service with sub-categories.
     */
    public function show(Service $service)
    {
        // Get sub-categories instead of individual samples
        $subCategories = $service->samples()
            ->where('is_active', true)
            ->whereNotNull('sub_category')
            ->select('sub_category')
            ->groupBy('sub_category')
            ->orderBy('sub_category')
            ->get()
            ->pluck('sub_category');

        return view('pages.service-detail', compact('service', 'subCategories'));
    }

    /**
     * Display samples from a specific sub-category.
     */
    public function showCategory(Service $service, $category)
    {
        $samples = $service->samples()
            ->where('is_active', true)
            ->where('sub_category', $category)
            ->orderBy('sort_order')
            ->get();

        if ($samples->isEmpty()) {
            abort(404);
        }

        return view('pages.service-category', compact('service', 'category', 'samples'));
    }

    /**
     * Display a specific sample detail.
     */
    public function showSample(Service $service, ServiceSample $sample)
    {
        // Ensure the sample belongs to this service
        if ($sample->service_id !== $service->id) {
            abort(404);
        }

        if (!$sample->is_active) {
            abort(404);
        }

        return view('pages.service-sample', compact('service', 'sample'));
    }

    /**
     * Get featured services for the home page.
     */
    public function featured()
    {
        $services = Service::featured()->active()->ordered()->take(3)->get();
        
        return response()->json($services);
    }

    /**
     * Get services by category.
     */
    public function byCategory($category)
    {
        $services = Service::active()
            ->where('category', $category)
            ->ordered()
            ->get();
        
        return response()->json($services);
    }
}
