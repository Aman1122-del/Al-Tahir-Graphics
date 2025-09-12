<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['admin', 'support']);
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->id ?? null;
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $serviceId,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_display' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,webp|max:2048',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            
            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',

            'samples' => 'array|max:10',
            'samples.*.id' => 'nullable|integer|exists:service_samples,id',
            'samples.*.title' => 'required|string|max:255',
            'samples.*.unit_price' => 'nullable|numeric|min:0',
            'samples.*.price_display' => 'nullable|string|max:255',
            'samples.*.sample_type' => 'nullable|string|max:100',
            'samples.*.sub_category' => 'nullable|string|max:100',
            'samples.*.description' => 'nullable|string|max:1000',
            'samples.*.is_active' => 'sometimes|boolean',
            'samples.*.sort_order' => 'nullable|integer|min:0',
            'samples.*.image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ];
    }
}


