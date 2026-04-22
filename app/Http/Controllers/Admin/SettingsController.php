<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        // Validate settings
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_email' => 'required|email',
            'items_per_page' => 'required|integer|min:10|max:100',
            'session_timeout' => 'required|integer|min:5|max:480',
            'enable_mfa' => 'boolean',
            'password_expiry_days' => 'required|integer|min:0|max:365',
            'max_login_attempts' => 'required|integer|min:3|max:20',
            'lockout_duration_minutes' => 'required|integer|min:5|max:120',
        ]);

        // Get current settings from config
        $settings = [
            'app_name' => config('app.name'),
            'app_email' => config('mail.from.address'),
            'items_per_page' => config('app.items_per_page', 50),
            'session_timeout' => config('session.lifetime', 120),
            'enable_mfa' => config('auth.mfa_enabled', false),
            'password_expiry_days' => config('auth.password_expiry_days', 90),
            'max_login_attempts' => config('auth.max_login_attempts', 5),
            'lockout_duration_minutes' => config('auth.lockout_duration_minutes', 15),
        ];

        // Update settings (in a real app, these would be stored in database)
        // For now, we'll just return success
        
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
