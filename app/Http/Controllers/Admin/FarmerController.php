<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\User;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    /**
     * Display a paginated listing of farmers with search and status filtering.
     * SRS §1.6: Admin can view, approve, or suspend Farmer registrations.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');
        $marketId = $request->input('market_id');

        $query = Farmer::with(['user', 'market'])->withCount(['products', 'orders']);

        // Filter by approval status
        if ($status === 'pending') {
            $query->where(function ($q) {
                $q->where('is_approved', false)
                  ->orWhere('approval_status', 'pending')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('is_approved', false);
                  });
            });
        } elseif ($status === 'approved' || $status === 'active') {
            $query->where('is_approved', true)
                  ->where(function ($q) {
                      $q->where('approval_status', 'approved')
                        ->orWhereNull('approval_status');
                  })
                  ->whereHas('user', function ($uq) {
                      $uq->where('is_approved', true)->where('is_active', true);
                  });
        } elseif ($status === 'suspended') {
            $query->where(function ($q) {
                $q->where('approval_status', 'suspended')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('is_active', false)->orWhere('status', 'suspended');
                  });
            });
        }

        // Filter by market
        if (!empty($marketId)) {
            $query->where('market_id', $marketId);
        }

        // Search by stall name, contact person, phone, or user email
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('stall_name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $farmers = $query->latest()->paginate(15)->withQueryString();

        // Metrics for filter tabs
        $counts = [
            'all' => Farmer::count(),
            'pending' => Farmer::where(function ($q) {
                $q->where('is_approved', false)
                  ->orWhere('approval_status', 'pending')
                  ->orWhereHas('user', fn($uq) => $uq->where('is_approved', false));
            })->count(),
            'approved' => Farmer::where('is_approved', true)
                ->where(function ($q) {
                    $q->where('approval_status', 'approved')->orWhereNull('approval_status');
                })
                ->whereHas('user', fn($uq) => $uq->where('is_approved', true)->where('is_active', true))
                ->count(),
            'suspended' => Farmer::where(function ($q) {
                $q->where('approval_status', 'suspended')
                  ->orWhereHas('user', fn($uq) => $uq->where('is_active', false)->orWhere('status', 'suspended'));
            })->count(),
        ];

        $markets = Market::orderBy('name')->get();

        return view('admin.farmers.index', compact('farmers', 'counts', 'status', 'search', 'marketId', 'markets'));
    }

    /**
     * Show detailed farmer profile, listings, and stall metadata.
     */
    public function show($id)
    {
        $farmer = Farmer::with(['user', 'market', 'products' => fn($q) => $q->latest()->take(10)])
            ->withCount(['products', 'orders'])
            ->findOrFail($id);

        return view('admin.farmers.show', compact('farmer'));
    }

    /**
     * Approve a pending farmer registration.
     */
    public function approve($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);

        $farmer->is_approved = true;
        $farmer->approval_status = 'approved';
        $farmer->rejection_reason = null;
        $farmer->save();

        if ($farmer->user) {
            $farmer->user->is_approved = true;
            $farmer->user->is_active = true;
            $farmer->user->status = 'active';
            $farmer->user->save();
        }

        return back()->with('success', "Stall '{$farmer->stall_name}' has been successfully approved. The farmer can now list fresh products and receive pre-orders.");
    }

    /**
     * Suspend an active farmer stall.
     */
    public function suspend(Request $request, $id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);

        $reason = $request->input('reason', 'Suspended by platform administrator.');

        $farmer->is_approved = false;
        $farmer->approval_status = 'suspended';
        $farmer->rejection_reason = $reason;
        $farmer->save();

        if ($farmer->user) {
            $farmer->user->is_approved = false;
            $farmer->user->is_active = false;
            $farmer->user->status = 'suspended';
            $farmer->user->save();
        }

        return back()->with('warning', "Stall '{$farmer->stall_name}' has been suspended from trading. Customer orders are temporarily locked.");
    }

    /**
     * Reinstate a previously suspended farmer stall.
     */
    public function reinstate($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);

        $farmer->is_approved = true;
        $farmer->approval_status = 'approved';
        $farmer->rejection_reason = null;
        $farmer->save();

        if ($farmer->user) {
            $farmer->user->is_approved = true;
            $farmer->user->is_active = true;
            $farmer->user->status = 'active';
            $farmer->user->save();
        }

        return back()->with('success', "Stall '{$farmer->stall_name}' has been reinstated and can resume fresh trading.");
    }

    /**
     * Remove or archive a farmer stall.
     */
    public function destroy($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);
        $stallName = $farmer->stall_name;

        // Ensure orders aren't orphaned; only allowed if 0 orders or marked archived
        if ($farmer->orders()->count() > 0) {
            // Safe disable rather than hard delete
            $farmer->is_approved = false;
            $farmer->approval_status = 'suspended';
            $farmer->save();
            if ($farmer->user) {
                $farmer->user->is_active = false;
                $farmer->user->status = 'suspended';
                $farmer->user->save();
            }
            return back()->with('info', "Stall '{$stallName}' has existing customer pre-orders and has been safely deactivated instead of deleted.");
        }

        $farmer->delete();

        return back()->with('success', "Farmer stall '{$stallName}' was successfully removed.");
    }
}
