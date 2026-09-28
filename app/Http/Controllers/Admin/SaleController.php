<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['farmer.user', 'farmer.market', 'products']);

        if ($request->filled('status')) {
            $today = now()->toDateString();
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'paused') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->whereDate('end_date', '<', $today);
            }
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('farmer', function ($fq) use ($q) {
                        $fq->where('stall_name', 'like', "%{$q}%")
                            ->orWhere('contact_person', 'like', "%{$q}%");
                    });
            });
        }

        $currentFeaturedSale = Sale::with(['farmer.market', 'products'])
            ->where('is_featured', true)
            ->first();

        $sales = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Sale::count(),
            'active' => Sale::where('is_active', true)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
            'featured' => Sale::where('is_featured', true)->count(),
        ];

        return view('admin.sales.index', compact('sales', 'currentFeaturedSale', 'stats'));
    }

    public function feature($id)
    {
        $sale = Sale::with('farmer')->findOrFail($id);

        // Turn off featured flag on all other sales (Single Spotlight Gate)
        Sale::where('id', '!=', $sale->id)->update(['is_featured' => false]);

        $sale->is_featured = true;
        $sale->is_active = true; // ensure active
        $sale->save();

        return redirect()->back()->with('success', "⭐ '{$sale->title}' by {$sale->farmer->stall_name} is now SPOTLIGHTED as the featured sale on the Homepage!");
    }

    public function unfeature($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->is_featured = false;
        $sale->save();

        return redirect()->back()->with('info', "Homepage spotlight removed for '{$sale->title}'.");
    }

    public function toggle($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->is_active = !$sale->is_active;
        if (!$sale->is_active && $sale->is_featured) {
            $sale->is_featured = false;
        }
        $sale->save();

        $status = $sale->is_active ? 'activated' : 'paused';
        return redirect()->back()->with('success', "Sale '{$sale->title}' has been {$status}.");
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $title = $sale->title;
        $sale->delete();

        return redirect()->back()->with('success', "Sale '{$title}' was deleted successfully.");
    }
}
