<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCustomerManagementController extends Controller
{
    /**
     * List customer accounts with order counts, search, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('orders')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $customers,
        ]);
    }

    /**
     * Show a single customer.
     */
    public function show($id): JsonResponse
    {
        $customer = User::where('role', 'customer')->find($id);
        if (! $customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data'   => $customer,
        ]);
    }

    /**
     * Activate or deactivate customer account access.
     */
    public function toggleStatus($id, Request $request): JsonResponse
    {
        $customer = User::where('role', 'customer')->find($id);

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $customer->status = $request->input('status');
        $customer->save();

        if ($customer->status !== 'active') {
            $customer->tokens()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Customer status updated to {$customer->status}.",
            'data'    => $customer,
        ]);
    }

    /**
     * Update a customer's basic information.
     * Only name, email, phone, and address can be changed.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $customer = User::where('role', 'customer')->find($id);
        if (! $customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => ['sometimes', 'string', 'max:255'],
            'email'   => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $customer->id],
            'phone'   => ['sometimes', 'string', 'max:20'],
            'address' => ['sometimes', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Update only allowed fields
        $customer->fill($validator->validated());
        $customer->save();

        return response()->json([
            'status' => 'success',
            'data'   => $customer,
        ]);
    }
}

