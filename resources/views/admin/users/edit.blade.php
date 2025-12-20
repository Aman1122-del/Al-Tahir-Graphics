@extends('layouts.admin')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Edit User - {{ $user->name }}</h1>
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.users.show', $user) }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                Back to User
                            </a>

                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- User Information -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold mb-4">User Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                            required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                        <input type="password" name="password"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New
                                            Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold mb-4">Assign Roles</h2>
                                <div class="space-y-3">
                                    @foreach ($roles as $role)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                id="role_{{ $role->id }}"
                                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ ucfirst($role->name) }}
                                            </label>
                                        </div>
                                    @endforeach
                                    @error('roles')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current User Info -->
                        <div class="bg-gray-50 p-6 rounded-lg mt-6">
                            <h2 class="text-xl font-semibold mb-4">Current User Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if ($user->email_verified_at)
                                            <span class="text-green-600">✓ Verified on
                                                {{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600">✗ Not Verified</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Current Roles</label>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($role->name === 'admin') bg-red-100 text-red-800
                                            @elseif($role->name === 'designer') bg-blue-100 text-blue-800
                                            @elseif($role->name === 'support') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
