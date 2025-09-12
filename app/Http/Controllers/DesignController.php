<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class DesignController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'design_json' => 'required|string',
            'title' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'price' => 'nullable|numeric|min:0',
            'preview_base64' => 'nullable|string',
            'metadata' => 'nullable|array',
            'design_id' => 'nullable|exists:designs,id',
        ]);

        $userId = Auth::id();

        $design = isset($validated['design_id'])
            ? Design::findOrFail($validated['design_id'])
            : new Design();

        if (!$design->exists) {
            $design->user_id = $userId;
        }

        $design->title = $validated['title'] ?? $design->title;
        $design->service_id = $validated['service_id'] ?? $design->service_id;
        $design->price = $validated['price'] ?? $design->price;
        $design->metadata = $validated['metadata'] ?? $design->metadata;

        // Store JSON
        $jsonPath = 'designs/json/' . ($design->exists ? $design->id : uniqid('temp_')) . '_' . time() . '.json';
        Storage::disk('private')->put($jsonPath, $validated['design_json']);
        $design->design_json_path = $jsonPath;

        // Store preview if provided
        if (!empty($validated['preview_base64'])) {
            $imageData = $validated['preview_base64'];
            if (preg_match('/^data:image\/(png|jpeg);base64,/', $imageData)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $binary = base64_decode($imageData);
            $imgPath = 'designs/previews/' . ($design->exists ? $design->id : uniqid('temp_')) . '_' . time() . '.png';
            Storage::disk('public')->put($imgPath, $binary);
            $design->preview_image_path = $imgPath;
        }

        $design->save();

        return response()->json([
            'success' => true,
            'design_id' => $design->id,
            'preview_url' => $design->preview_image_path ? Storage::url($design->preview_image_path) : null,
        ]);
    }

    public function load(Design $design): JsonResponse
    {
        $json = Storage::disk('private')->get($design->design_json_path);
        return response()->json([
            'success' => true,
            'design' => $design,
            'design_json' => $json,
            'preview_url' => $design->preview_image_path ? Storage::url($design->preview_image_path) : null,
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:png,svg,pdf',
            'data' => 'required|string',
            'filename' => 'nullable|string|max:255'
        ]);

        $filename = ($validated['filename'] ?? 'design') . '.' . $validated['format'];
        $path = 'designs/exports/' . uniqid() . '_' . $filename;

        if ($validated['format'] === 'png') {
            $data = $validated['data'];
            if (preg_match('/^data:image\/png;base64,/', $data)) {
                $data = substr($data, strpos($data, ',') + 1);
            }
            Storage::disk('public')->put($path, base64_decode($data));
        } else {
            // svg or pdf as raw data string
            Storage::disk('public')->put($path, $validated['data']);
        }

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
        ]);
    }
}


