<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSetting;
use Illuminate\Http\Request;

class ChatSettingsController extends Controller
{
    public function edit()
    {
        $settings = ChatSetting::query()->first();
        return view('admin.chat-settings', ['settings' => $settings]);
    }

    public function save(Request $request)
    {
        $settings = ChatSetting::query()->first() ?: new ChatSetting();
        $settings->force_bot_only = (bool) $request->boolean('force_bot_only');
        $settings->force_live_only = (bool) $request->boolean('force_live_only');
        $rawHours = $request->input('business_hours');
        $decoded = [];
        if ($rawHours) {
            try { $decoded = json_decode($rawHours, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) { $decoded = []; }
        }
        $settings->business_hours = $decoded;
        $settings->save();
        return redirect()->route('admin.chat.settings')->with('status', 'Settings saved');
    }
}


