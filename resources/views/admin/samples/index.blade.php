@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="section-title">Service Samples</h1>
        <a href="{{ route('admin.services.index') }}" class="btn-primary">Back to Services</a>
    </div>

    <div class="bg-white rounded-2xl shadow ring-1 ring-black/5">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($samples as $sample)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}" class="w-16 h-16 rounded-lg object-cover">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $sample->title }}</div>
                        <div class="text-sm text-gray-500">{{ $sample->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.services.edit', $sample->service) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $sample->service->title }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $sample->sub_category }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $sample->formatted_price }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $sample->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $sample->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('admin.samples.edit', $sample) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="{{ route('admin.samples.destroy', $sample) }}" method="POST" class="inline" onsubmit="return confirm('Delete sample?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $samples->links() }}
    </div>
</div>
@endsection
