<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $service->load(['samples' => function($q){ $q->where('is_active', true)->orderBy('sort_order')->limit(10); }]);
        return view('pages.service-detail', compact('service'));
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
