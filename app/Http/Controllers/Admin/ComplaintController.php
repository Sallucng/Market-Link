<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Farmer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['customer', 'farmer.market', 'order']);

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'under_review', 'resolved', 'dismissed'])) {
            $query->where('status', $request->status);
        }

        // Filter by farmer
        if ($request->filled('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }

        // Filter by complaint type
        if ($request->filled('type')) {
            $query->where('complaint_type', $request->type);
        }

        // Search in subject, description, customer name, farmer stall name
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('subject', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('name', 'like', $term)
                         ->orWhere('email', 'like', $term);
                  })
                  ->orWhereHas('farmer', function ($fq) use ($term) {
                      $fq->where('stall_name', 'like', $term)
                         ->orWhere('contact_person', 'like', $term);
                  });
            });
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::pending()->count(),
            'under_review' => Complaint::underReview()->count(),
            'resolved' => Complaint::resolved()->count(),
            'dismissed' => Complaint::dismissed()->count(),
        ];

        $farmers = Farmer::where('is_approved', true)->orderBy('stall_name')->get();

        return view('admin.complaints.index', compact('complaints', 'stats', 'farmers'));
    }

    public function show($id)
    {
        $complaint = Complaint::with(['customer', 'farmer.market', 'farmer.user', 'order.items.product', 'resolver'])
            ->findOrFail($id);

        // Previous complaints against this same farmer for admin reference
        $farmerComplaintHistory = Complaint::where('farmer_id', $complaint->farmer_id)
            ->where('id', '!=', $complaint->id)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.complaints.show', compact('complaint', 'farmerComplaintHistory'));
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $previousStatus = $complaint->status;
        $complaint->status = $request->status;
        $complaint->admin_notes = $request->admin_notes;

        if (in_array($request->status, ['resolved', 'dismissed'])) {
            $complaint->resolved_at = now();
            $complaint->resolved_by = Auth::id();
        } else {
            $complaint->resolved_at = null;
            $complaint->resolved_by = null;
        }

        $complaint->save();

        // Notify customer about status change
        if ($previousStatus !== $request->status) {
            $statusLabel = match ($request->status) {
                'under_review' => 'Under Active Review',
                'resolved' => 'Resolved',
                'dismissed' => 'Reviewed & Closed',
                default => 'Pending Review',
            };

            Notification::create([
                'user_id' => $complaint->customer_id,
                'title' => "Complaint #CMP-{$complaint->id} Status Updated",
                'message' => "Your complaint regarding {$complaint->farmer->stall_name} is now marked as {$statusLabel} by MarketLink Administration.",
                'type' => 'complaint',
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.complaints.show', $complaint->id)
            ->with('success', "Complaint #CMP-{$complaint->id} status updated to {$complaint->status}.");
    }

    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', "Complaint record #CMP-{$id} has been removed.");
    }
}
