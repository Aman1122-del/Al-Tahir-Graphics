@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="section-title mb-6">Create Service</h1>

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Title</label>
                <input name="title" class="form-input" required />
            </div>
            <div>
                <label class="form-label">Slug (optional)</label>
                <input name="slug" class="form-input" />
            </div>
            <div>
                <label class="form-label">Base Price</label>
                <input name="price" type="number" step="0.01" class="form-input" required />
            </div>
            <div>
                <label class="form-label">Price Display (optional)</label>
                <input name="price_display" class="form-input" placeholder="e.g., Starting from PKR 500" />
            </div>
            <div>
                <label class="form-label">Category</label>
                <input name="category" class="form-input" />
            </div>
            <div>
                <label class="form-label">Sort Order</label>
                <input name="sort_order" type="number" class="form-input" value="0" />
            </div>
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea"></textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Main Image</label>
                <input type="file" name="image" accept="image/*" class="form-input" />
            </div>
            <div>
                <label class="form-label">Gallery Images (up to 10)</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="form-input" />
            </div>
        </div>

        <!-- SEO Section -->
        <div class="rounded-xl bg-blue-50 p-4 ring-1 ring-blue-200">
            <h3 class="font-semibold mb-3 text-blue-900">SEO Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Meta Title</label>
                    <input name="meta_title" class="form-input" maxlength="255" />
                </div>
                <div>
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-textarea" maxlength="500"></textarea>
                </div>
                <div>
                    <label class="form-label">Meta Keywords</label>
                    <textarea name="meta_keywords" rows="2" class="form-textarea" maxlength="1000" placeholder="Separate keywords with commas"></textarea>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <label class="inline-flex items-center"><input type="checkbox" name="is_active" value="1" checked class="mr-2"> Active</label>
            <label class="inline-flex items-center"><input type="checkbox" name="is_featured" value="1" class="mr-2"> Featured</label>
        </div>

        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-black/5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Samples (up to 10)</h3>
                <button type="button" id="addSample" class="btn-primary">Add Sample</button>
            </div>
            <div id="samplesContainer" class="space-y-3"></div>
        </div>

        <div class="flex justify-end">
            <button class="btn-primary">Create</button>
        </div>
    </form>
</div>

<script>
document.getElementById('addSample').addEventListener('click', function(){
    const container = document.getElementById('samplesContainer');
    const index = container.children.length;
    if(index >= 10) return;
    const row = document.createElement('div');
    row.innerHTML = `
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="grid gap-3 md:grid-cols-3 mb-3">
                <div>
                    <label class="form-label">Title</label>
                    <input name="samples[${index}][title]" class="form-input" required />
                </div>
                <div>
                    <label class="form-label">Unit Price</label>
                    <input name="samples[${index}][unit_price]" type="number" step="0.01" class="form-input" />
                </div>
                <div>
                    <label class="form-label">Price Display</label>
                    <input name="samples[${index}][price_display]" class="form-input" placeholder="Custom price text" />
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-3 mb-3">
                <div>
                    <label class="form-label">Sample Type</label>
                    <select name="samples[${index}][sample_type]" class="form-input">
                        <option value="standard">Standard</option>
                        <option value="premium">Premium</option>
                        <option value="deluxe">Deluxe</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Sub Category</label>
                    <input name="samples[${index}][sub_category]" class="form-input" />
                </div>
                <div>
                    <label class="form-label">Sort Order</label>
                    <input name="samples[${index}][sort_order]" type="number" class="form-input" value="${index}" />
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2 mb-3">
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="samples[${index}][description]" rows="2" class="form-textarea"></textarea>
                </div>
                <div>
                    <label class="form-label">Image</label>
                    <input type="file" name="samples[${index}][image]" accept="image/*" class="form-input" />
                </div>
            </div>
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="samples[${index}][is_active]" value="1" checked class="mr-2"> Active
                </label>
                <button type="button" onclick="this.closest('.bg-white').remove()" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
            </div>
        </div>`;
    container.appendChild(row);
});
</script>
@endsection


