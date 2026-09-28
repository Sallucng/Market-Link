<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show all platform settings grouped logically.
     */
    public function index()
    {
        $settingsCollection = Setting::orderBy('sort_order')->get();
        $settings = $settingsCollection->pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings', 'settingsCollection'));
    }

    /**
     * Update all submitted platform settings.
     */
    public function update(Request $request)
    {
        $payload = $request->has('settings') && is_array($request->input('settings'))
            ? $request->input('settings')
            : $request->all();

        $validator = validator($payload, [
            'default_cutoff_hours' => 'nullable|integer|min:1|max:72',
            'max_preorder_days' => 'nullable|integer|min:1|max:30',
            'min_platform_order' => 'nullable|numeric|min:0',
            'max_active_listings_per_farmer' => 'nullable|integer|min:1|max:500',
            'pickup_reminder_lead_hours' => 'nullable|integer|min:1|max:48',
            'order_cancellation_grace_hours' => 'nullable|integer|min:1|max:48',
            'low_stock_threshold_default' => 'nullable|integer|min:0|max:100',
            'site_title' => 'nullable|string|max:120',
            'support_email' => 'nullable|email|max:120',
            'contact_phone' => 'nullable|string|max:30',
            'currency_symbol' => 'nullable|string|max:10',
            'tax_rate_percent' => 'nullable|numeric|min:0|max:100',
            'emergency_broadcast_message' => 'nullable|string|max:500',
        ]);

        $validated = $validator->validate();

        // Integer and string fields
        foreach ($validated as $key => $val) {
            Setting::set($key, $val);
        }

        // Boolean toggles (checkboxes)
        $toggles = [
            'allow_same_day_orders',
            'auto_approve_farmers',
            'require_stall_coordinates',
            'allow_farmer_order_cancellation',
            'require_stock_tracking',
            'enable_in_app_notifications',
            'admin_email_alerts',
            'maintenance_mode',
            'emergency_broadcast_active',
            'auto_publish_reviews',
            'enable_ai_chatbot',
        ];

        foreach ($toggles as $toggleKey) {
            Setting::set($toggleKey, $request->has($toggleKey) ? '1' : '0');
        }

        return redirect()->route('admin.settings.index')->with('success', 'Platform settings and system policies updated successfully!');
    }

    /**
     * Instant toggle for quick dashboard switches.
     */
    public function quickToggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $key = $request->input('key');
        $value = $request->input('value');

        Setting::set($key, $value);

        return response()->json([
            'success' => true,
            'key' => $key,
            'value' => $value,
            'message' => 'Setting updated instantly.',
        ]);
    }
}
