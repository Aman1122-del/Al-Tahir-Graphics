<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceSample;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of all active services with samples.
     */
    public function index()
    {
        $services = Service::active()->ordered()->get();
        
        // Get all active samples across all services for samples-first display
        $allSamples = ServiceSample::with('service')
            ->where('is_active', true)
            ->whereHas('service', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get unique categories across all samples
        $allCategories = $allSamples->whereNotNull('sub_category')
            ->pluck('sub_category')
            ->unique()
            ->sort()
            ->values();
            
        // Get unique service types
        $serviceTypes = $services->whereNotNull('category')
            ->pluck('category')
            ->unique()
            ->sort()
            ->values();
        
        return view('pages.services', compact('services', 'allSamples', 'allCategories', 'serviceTypes'));
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
