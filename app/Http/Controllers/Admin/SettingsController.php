<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    protected SettingsService $settings;

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    public function index()
    {
        $settings = $this->settings->all();

        try {
            $dbVersion = DB::select('SELECT sqlite_version() as version')[0]->version ?? 'N/A';
            $dbDriver = 'SQLite';
        } catch (\Exception $e) {
            $dbVersion = 'N/A';
            $dbDriver = config('database.default', 'unknown');
        }

        return view('admin.settings.index', compact('settings', 'dbVersion', 'dbDriver'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name'                 => 'required|string|max:255',
            'app_email'                => 'required|email',
            'items_per_page'           => 'required|integer|min:10|max:100',
            'session_timeout'          => 'required|integer|min:5|max:480',
            'enable_mfa'               => 'nullable',
            'password_expiry_days'     => 'required|integer|min:0|max:365',
            'max_login_attempts'       => 'required|integer|min:3|max:20',
            'lockout_duration_minutes' => 'required|integer|min:5|max:120',
            'evidence_instructions'    => 'nullable|string|max:2000',
            'cross_institution_notify' => 'nullable',
        ]);

        $validated['enable_mfa']               = $request->boolean('enable_mfa');
        $validated['cross_institution_notify'] = $request->boolean('cross_institution_notify');

        $this->settings->set($validated);

        auth()->user()->logActivity('settings_updated', 'success', ['updated_by' => auth()->id()]);

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            auth()->user()->logActivity('cache_cleared', 'success', ['cleared_by' => auth()->id()]);

            return response()->json(['success' => true, 'message' => 'Cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear cache: ' . $e->getMessage()], 500);
        }
    }
}
