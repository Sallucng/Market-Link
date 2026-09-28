<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    /**
     * List notifications (optionally filtered by user_id).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $notifications = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data'   => $notifications,
        ]);
    }

    /**
     * Create a new notification for a given user.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'title'   => ['required', 'string', 'max:255'],
            'body'    => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $notification = Notification::create($validator->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification created.',
            'data'    => $notification,
        ], 201);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead($id): JsonResponse
    {
        $notification = Notification::find($id);
        if (! $notification) {
            return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
        }
        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all (or all for a specific user) notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $query = Notification::where('is_read', false);
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        $query->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::find($id);
        if (! $notification) {
            return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
        }
        $notification->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification deleted.',
        ]);
    }
}
