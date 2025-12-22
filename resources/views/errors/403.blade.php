@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h1 class="text-xl font-semibold text-red-800">Access Denied</h1>
            </div>

            <p class="text-red-700 mb-4">{{ $message ?? 'You do not have permission to access this resource.' }}</p>

            @if(config('app.debug') && isset($debug))
                <div class="bg-gray-100 p-4 rounded-md mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Debug Information:</h3>
                    <pre class="text-sm text-gray-700">{{ json_encode($debug, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            <div class="flex space-x-4">
                <a href="{{ route('returns.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    ← Back to My Returns
                </a>
                <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
