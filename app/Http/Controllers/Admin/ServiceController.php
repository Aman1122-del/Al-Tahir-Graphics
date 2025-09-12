<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function toggle(Service $service)
    {
        $this->authorize('manage services');

        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'success' => true,
            'service' => $service->only(['id', 'title', 'is_active'])
        ]);
    }
}


