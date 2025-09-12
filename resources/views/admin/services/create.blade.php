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
                <label class="form-label">Price</label>
                <input name="price" type="number" step="0.01" class="form-input" required />
            </div>
            <div>
                <label class="form-label">Category</label>
                <input name="category" class="form-input" />
            </div>
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea"></textarea>
        </div>

        <div>
            <label class="form-label">Image</label>
            <input type="file" name="image" accept="image/*" />
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
    row.className = 'grid gap-3 md:grid-cols-5 items-end';
    row.innerHTML = `
        <div class="md:col-span-2">
            <label class="form-label">Title</label>
            <input name="samples[${index}][title]" class="form-input" required />
        </div>
        <div>
            <label class="form-label">Price</label>
            <input name="samples[${index}][price]" type="number" step="0.01" class="form-input" />
        </div>
        <div>
            <label class="form-label">Image</label>
            <input type="file" name="samples[${index}][image]" accept="image/*" />
        </div>
        <div class="flex items-center gap-3">
            <label class="inline-flex items-center"><input type="checkbox" name="samples[${index}][is_active]" value="1" checked class="mr-2"> Active</label>
            <input type="number" name="samples[${index}][sort_order]" class="form-input w-20" placeholder="#" />
        </div>`;
    container.appendChild(row);
});
</script>
@endsection


