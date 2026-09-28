<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a paginated list of registered customers.
     * SRS §1.6: Admin customer moderation and status controls.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = User::where('role', 'customer')->withCount('orders');

        // Status filter
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'suspended' || $status === 'deactivated') {
            $query->where('is_active', false);
        }

        // Search query
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all' => User::where('role', 'customer')->count(),
            'active' => User::where('role', 'customer')->where('is_active', true)->count(),
            'suspended' => User::where('role', 'customer')->where('is_active', false)->count(),
        ];

        return view('admin.customers.index', compact('customers', 'counts', 'status', 'search'));
    }

    /**
     * Toggle customer account active/suspended state.
     */
    public function toggleStatus($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->status = $customer->is_active ? 'active' : 'suspended';
        $customer->save();

        $stateText = $customer->is_active ? 'activated' : 'suspended';
        $alertType = $customer->is_active ? 'success' : 'warning';

        return back()->with($alertType, "Customer account '{$customer->name}' ({$customer->email}) has been {$stateText}.");
    }

    /**
     * Remove or archive customer account.
     */
    public function destroy($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $name = $customer->name;

        // If customer has orders, deactivate instead of hard delete
        if ($customer->orders()->count() > 0) {
            $customer->is_active = false;
            $customer->status = 'suspended';
            $customer->save();
            return back()->with('info', "Customer '{$name}' has past order records and has been deactivated instead of permanently deleted.");
        }

        $customer->delete();
        return back()->with('success', "Customer '{$name}' was permanently removed.");
    }
}
