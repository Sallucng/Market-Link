<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FarmerAuthController extends Controller
{
    /**
     * Register a new Farmer account with associated business profile.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'farm_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'stall_number' => ['nullable', 'string', 'max:50'],
            'business_license' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'operating_days' => ['nullable', 'array'],
            'operating_days.*' => ['string'],
            'pickup_window_start' => ['nullable', 'date_format:H:i'],
            'pickup_window_end' => ['nullable', 'date_format:H:i'],
            'order_cutoff_time' => ['nullable', 'date_format:H:i'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role' => 'farmer',
                'status' => 'pending', // Pending admin approval
            ]);

            $profile = FarmerProfile::create([
                'user_id' => $user->id,
                'farm_name' => $validated['farm_name'],
                'bio' => $validated['bio'] ?? null,
                'stall_number' => $validated['stall_number'] ?? null,
                'business_license' => $validated['business_license'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'operating_days' => $validated['operating_days'] ?? [],
                'pickup_window_start' => $validated['pickup_window_start'] ?? null,
                'pickup_window_end' => $validated['pickup_window_end'] ?? null,
                'order_cutoff_time' => $validated['order_cutoff_time'] ?? null,
                'approval_status' => 'pending',
            ]);

            $token = $user->createToken('farmer-token')->plainTextToken;

            return [
                'user' => $user->load('farmerProfile'),
                'token' => $token,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer registered successfully. Your profile is currently pending admin approval.',
            'data' => $result,
        ], 201);
    }

    /**
     * Authenticate farmer.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if (!$user->isFarmer()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Account is not registered as a farmer.',
            ], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been suspended by administration.',
            ], 403);
        }

        $token = $user->createToken('farmer-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'user' => $user->load('farmerProfile.markets'),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout farmer.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get authenticated farmer info.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['farmerProfile.markets']);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }
}
