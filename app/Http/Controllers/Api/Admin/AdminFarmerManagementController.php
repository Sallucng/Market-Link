<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminFarmerManagementController extends Controller
{
    /**
     * List all registered farmers with approval/suspension status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FarmerProfile::with(['user:id,name,email,phone,status,created_at', 'markets']);

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('user', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $farmers = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $farmers,
        ]);
    }

    /**
     * View farmer details.
     */
    public function show($id): JsonResponse
    {
        $farmer = FarmerProfile::with([
            'user',
            'markets',
            'products.category',
            'orders' => function ($q) {
                $q->latest()->limit(10);
            },
        ])->find($id);

        if (!$farmer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $farmer,
        ]);
    }

    /**
     * Approve farmer account and profile (active).
     */
    public function approve($id): JsonResponse
    {
        $farmer = FarmerProfile::with('user')->find($id);

        if (!$farmer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        DB::transaction(function () use ($farmer) {
            $farmer->is_approved = true;
            $farmer->approval_status = 'approved';
            $farmer->rejection_reason = null;
            $farmer->save();

            if ($farmer->user) {
                $farmer->user->status = 'active';
                $farmer->user->save();
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Farmer profile approved successfully.',
            'data'    => $farmer->fresh('user'),
        ]);
    }

    /**
     * Suspend farmer account and set all products to unavailable.
     */
    public function suspend($id, Request $request): JsonResponse
    {
        $farmer = FarmerProfile::with(['user', 'products'])->find($id);

        if (!$farmer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        DB::transaction(function () use ($farmer, $request) {
            $reason = $request->input('reason', 'Suspended by platform administrator.');

            $farmer->is_approved = false;
            $farmer->approval_status = 'suspended';
            $farmer->rejection_reason = $reason;
            $farmer->save();

            // Set all products of suspended farmer to unavailable
            $farmer->products()->update(['status' => 'unavailable']);

            // Suspend user account and revoke API tokens
            if ($farmer->user) {
                $farmer->user->status = 'suspended';
                $farmer->user->save();
                $farmer->user->tokens()->delete();
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Farmer account suspended and products marked unavailable.',
            'data'    => $farmer->fresh('user'),
        ]);
    }

    /**
     * Reject farmer registration application.
     */
    public function reject($id, Request $request): JsonResponse
    {
        $farmer = FarmerProfile::with('user')->find($id);

        if (!$farmer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::transaction(function () use ($farmer, $request) {
            $farmer->is_approved = false;
            $farmer->approval_status = 'rejected';
            $farmer->rejection_reason = $request->rejection_reason;
            $farmer->save();

            if ($farmer->user) {
                $farmer->user->status = 'suspended';
                $farmer->user->save();
                $farmer->user->tokens()->delete();
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Farmer registration application has been rejected.',
            'data'    => $farmer->fresh('user'),
        ]);
    }
}
