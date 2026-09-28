<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\ComplaintSubmittedMail;
use App\Models\Complaint;
use App\Models\Farmer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $complaints = Complaint::where('customer_id', $user->id)
            ->with(['farmer', 'order'])
            ->latest()
            ->paginate(10);

        $counts = [
            'total' => Complaint::where('customer_id', $user->id)->count(),
            'pending' => Complaint::where('customer_id', $user->id)->where('status', 'pending')->count(),
            'under_review' => Complaint::where('customer_id', $user->id)->where('status', 'under_review')->count(),
            'resolved' => Complaint::where('customer_id', $user->id)->where('status', 'resolved')->count(),
        ];

        return view('customer.complaints.index', compact('complaints', 'counts'));
    }

    public function create(Request $request)
    {
        $preselectedFarmer = null;
        $preselectedOrder = null;

        if ($request->has('order_id')) {
            $preselectedOrder = Order::where('customer_id', Auth::id())
                ->with('farmer')
                ->find($request->order_id);
            if ($preselectedOrder) {
                $preselectedFarmer = $preselectedOrder->farmer;
            }
        } elseif ($request->has('farmer_id')) {
            $preselectedFarmer = Farmer::find($request->farmer_id);
        }

        $farmers = Farmer::where('is_approved', true)->orderBy('stall_name')->get();
        $customerOrders = Order::where('customer_id', Auth::id())
            ->with('farmer')
            ->latest()
            ->take(20)
            ->get();

        return view('customer.complaints.create', compact('preselectedFarmer', 'preselectedOrder', 'farmers', 'customerOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'order_id' => 'nullable|exists:orders,id',
            'complaint_type' => 'required|in:poor_quality,unfulfilled_order,pricing_issue,unprofessional_conduct,inaccurate_listing,other',
            'subject' => 'required|string|min:3|max:150',
            'description' => 'required|string|min:10|max:3000',
        ]);

        if ($request->order_id) {
            $order = Order::where('customer_id', Auth::id())->find($request->order_id);
            if (!$order) {
                return back()->withInput()->with('error', 'Selected order was not found or does not belong to your account.');
            }
        }

        $complaint = Complaint::create([
            'customer_id' => Auth::id(),
            'farmer_id' => $request->farmer_id,
            'order_id' => $request->order_id,
            'complaint_type' => $request->complaint_type,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Eager load farmer and customer for notifications and mail
        $complaint->load(['farmer', 'customer', 'order']);

        // In-app customer notification
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Complaint Received for Moderation',
            'message' => "Your complaint regarding {$complaint->farmer->stall_name} has been filed under case #CMP-{$complaint->id}. Our administration team is reviewing it.",
            'type' => 'complaint',
            'is_read' => false,
        ]);

        // Notify Admin via Resend Email (safely handled to avoid halting on sandboxed accounts)
        try {
            $adminEmail = config('mail.admin_address') 
                ?? User::where('role', 'admin')->value('email') 
                ?? 'admin@marketlink.local';

            // Attempt delivery via Resend
            Mail::to($adminEmail)->send(new ComplaintSubmittedMail($complaint));
        } catch (\Throwable $e) {
            Log::warning('Resend email delivery skipped or encountered an error: ' . $e->getMessage());
        }

        return redirect()->route('customer.complaints.show', $complaint->id)
            ->with('success', 'Your complaint has been submitted confidentially to MarketLink administration. Normal users and farmers cannot view this report.');
    }

    public function show($id)
    {
        $complaint = Complaint::where('customer_id', Auth::id())
            ->with(['farmer.market', 'order.items.product'])
            ->findOrFail($id);

        return view('customer.complaints.show', compact('complaint'));
    }
}
