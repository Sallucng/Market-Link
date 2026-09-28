<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected function getFarmer()
    {
        $farmer = Farmer::where('user_id', Auth::id())->first();
        if (!$farmer) {
            abort(403, 'Farmer profile not found.');
        }
        return $farmer;
    }

    public function edit()
    {
        $farmer = $this->getFarmer();
        $markets = Market::orderBy('name')->get();
        return view('farmer.profile', compact('farmer', 'markets'));
    }

    public function update(Request $request)
    {
        $farmer = $this->getFarmer();

        $validated = $request->validate([
            'stall_name' => 'required|string|max:100',
            'contact_person' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'market_id' => 'nullable|exists:markets,id',
            'address' => 'required|string|max:255',
            'operating_days' => 'required|string|max:100',
            'pickup_time_windows' => 'required|string|max:255',
            'cutoff_hours' => 'required|integer|min:0|max:48',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'bio' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url|max:500',
        ]);

        $farmer->update($validated);

        // Also update contact info in user account
        $farmer->user->update([
            'name' => $validated['contact_person'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
        ]);

        return back()->with('success', 'Stall profile and market pickup details updated successfully!');
    }

    /**
     * Show Farmer Stall Configurations & Operations Hub.
     */
    public function settings()
    {
        $farmer = $this->getFarmer();
        $settings = $farmer->settings ?? [];

        return view('farmer.settings.index', compact('farmer', 'settings'));
    }

    /**
     * Save all Farmer Stall Configurations.
     */
    public function updateSettings(Request $request)
    {
        $farmer = $this->getFarmer();

        $validated = $request->validate([
            'cutoff_hours' => 'required|integer|min:0|max:72',
            'low_stock_threshold' => 'nullable|integer|min:1|max:50',
            'max_daily_orders' => 'nullable|integer|min:5|max:500',
            'min_order_amount' => 'nullable|numeric|min:0',
            'eco_packaging_note' => 'nullable|string|max:300',
            'min_pickup_lead_minutes' => 'nullable|integer|min:0|max:360',
            'vacation_notice' => 'nullable|string|max:500',
            'vacation_return_date' => 'nullable|date',
            'pickup_time_windows' => 'nullable|string|max:255',
        ]);

        $settings = $farmer->settings ?? [];

        // Save numeric and text configurations
        $settings['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $settings['max_daily_orders'] = (int) ($validated['max_daily_orders'] ?? 50);
        $settings['min_order_amount'] = (float) ($validated['min_order_amount'] ?? 0);
        $settings['eco_packaging_note'] = $validated['eco_packaging_note'] ?? '';
        if (isset($validated['min_pickup_lead_minutes'])) {
            $settings['min_pickup_lead_minutes'] = (int) $validated['min_pickup_lead_minutes'];
        }
        $settings['vacation_notice'] = $validated['vacation_notice'] ?? '';
        $settings['vacation_return_date'] = $validated['vacation_return_date'] ?? '';

        // Toggles
        $toggles = [
            'stall_open',
            'auto_accept_orders',
            'auto_sold_out',
            'allow_customer_notes',
            'allow_substitutions',
            'order_alert_sound',
            'vacation_mode',
            'email_notifications',
            'sms_notifications',
        ];

        foreach ($toggles as $tog) {
            $settings[$tog] = $request->has($tog);
        }

        $farmer->settings = $settings;
        $farmer->cutoff_hours = (int) $validated['cutoff_hours'];
        if (!empty($validated['pickup_time_windows'])) {
            $farmer->pickup_time_windows = $validated['pickup_time_windows'];
        }
        $farmer->save();

        return redirect()->route('farmer.settings.index')->with('success', 'Stall operational configurations updated successfully!');
    }

    /**
     * AJAX quick toggle for dashboard switches.
     */
    public function quickToggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $farmer = $this->getFarmer();
        $key = $request->input('key');
        $value = $request->input('value');

        if ($key === 'cutoff_hours') {
            $farmer->cutoff_hours = (int) $value;
            $farmer->save();
        } else {
            $settings = $farmer->settings ?? [];
            $settings[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $farmer->settings = $settings;
            $farmer->save();
        }

        return response()->json([
            'success' => true,
            'key' => $key,
            'value' => $value,
            'message' => 'Stall setting updated instantly.',
        ]);
    }
}
