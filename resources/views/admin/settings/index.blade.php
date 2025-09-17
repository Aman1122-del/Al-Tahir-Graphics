@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">System Settings</h1>
                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                Clear Cache
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.settings.backup-database') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                Backup Database
                            </button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    <!-- General Settings -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h2 class="text-xl font-semibold mb-4">General Settings</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site Name *</label>
                                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']->value ?? config('app.name')) }}" required
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('site_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site Email *</label>
                                <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']->value ?? config('mail.from.address')) }}" required
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('site_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site Phone</label>
                                <input type="text" name="site_phone" value="{{ old('site_phone', $settings['site_phone']->value ?? '') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('site_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                                <select name="currency" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="PKR" {{ old('currency', $settings['currency']->value ?? 'PKR') === 'PKR' ? 'selected' : '' }}>PKR - Pakistani Rupee</option>
                                    <option value="USD" {{ old('currency', $settings['currency']->value ?? 'PKR') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency', $settings['currency']->value ?? 'PKR') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Timezone *</label>
                                <select name="timezone" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Asia/Karachi" {{ old('timezone', $settings['timezone']->value ?? 'Asia/Karachi') === 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi</option>
                                    <option value="UTC" {{ old('timezone', $settings['timezone']->value ?? 'Asia/Karachi') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone', $settings['timezone']->value ?? 'Asia/Karachi') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                </select>
                                @error('timezone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Address</label>
                            <textarea name="site_address" rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Enter your business address...">{{ old('site_address', $settings['site_address']->value ?? '') }}</textarea>
                            @error('site_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h2 class="text-xl font-semibold mb-4">System Settings</h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="maintenance_mode" value="1" 
                                       {{ old('maintenance_mode', $settings['maintenance_mode']->value ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Enable Maintenance Mode</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="registration_enabled" value="1" 
                                       {{ old('registration_enabled', $settings['registration_enabled']->value ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Allow User Registration</label>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h2 class="text-xl font-semibold mb-4">Notification Settings</h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="email_notifications" value="1" 
                                       {{ old('email_notifications', $settings['email_notifications']->value ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Enable Email Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="order_notifications" value="1" 
                                       {{ old('order_notifications', $settings['order_notifications']->value ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Order Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="quote_notifications" value="1" 
                                       {{ old('quote_notifications', $settings['quote_notifications']->value ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Quote Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="invoice_notifications" value="1" 
                                       {{ old('invoice_notifications', $settings['invoice_notifications']->value ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label class="ml-2 text-sm text-gray-700">Invoice Notifications</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
