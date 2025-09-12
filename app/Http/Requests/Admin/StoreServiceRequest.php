<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['admin', 'support']);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',

            'samples' => 'array|max:10',
            'samples.*.id' => 'nullable|integer|exists:service_samples,id',
            'samples.*.title' => 'required|string|max:255',
            'samples.*.price' => 'nullable|numeric|min:0',
            'samples.*.is_active' => 'sometimes|boolean',
            'samples.*.sort_order' => 'nullable|integer|min:0',
            'samples.*.image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ];
    }
}


