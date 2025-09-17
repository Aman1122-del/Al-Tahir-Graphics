<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string|max:50',
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'order_notifications' => 'boolean',
            'quote_notifications' => 'boolean',
            'invoice_notifications' => 'boolean',
        ]);

        $settings = [
            'site_name' => $request->site_name,
            'site_email' => $request->site_email,
            'site_phone' => $request->site_phone,
            'site_address' => $request->site_address,
            'currency' => $request->currency,
            'timezone' => $request->timezone,
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            'registration_enabled' => $request->boolean('registration_enabled'),
            'email_notifications' => $request->boolean('email_notifications'),
            'order_notifications' => $request->boolean('order_notifications'),
            'quote_notifications' => $request->boolean('quote_notifications'),
            'invoice_notifications' => $request->boolean('invoice_notifications'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_updated',
            'model_type' => Setting::class,
            'model_id' => null,
            'new_values' => $settings,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Settings updated successfully.');
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return back()->with('success', 'Cache cleared successfully.');
    }

    public function backupDatabase()
    {
        try {
            \Artisan::call('backup:run');
            return back()->with('success', 'Database backup created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create database backup: ' . $e->getMessage());
        }
    }
}
