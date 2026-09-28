<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * List all users with optional role filter
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Optional filter by role
        if ($request->filled('role')) {
            $query->where(
                'role',
                $request->query('role')
            );
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($users, 200);
    }

    /**
     * View a specific user
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Update user role or activate / deactivate account
     */
    public function update(
        UpdateAdminUserRequest $request,
        $id
    ): JsonResponse {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh(),
        ], 200);
    }
}
