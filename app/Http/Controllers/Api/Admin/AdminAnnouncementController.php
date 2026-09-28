<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    /**
     * List all platform announcements.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with('admin:id,name,email');

        if ($request->filled('target_role')) {
            $query->where('target_role', $request->target_role);
        } elseif ($request->filled('target_audience')) {
            $query->where('target_role', $request->target_audience);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $announcements = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $announcements,
        ]);
    }

    /**
     * Create and publish a platform announcement.
     */
    public function store(AdminAnnouncementRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $target = $validated['target_role'] ?? $validated['target_audience'] ?? 'all';

        $announcement = Announcement::create([
            'admin_id'    => $request->user()->id,
            'title'       => $validated['title'],
            'message'     => $validated['message'],
            'target_role' => $target,
            'is_active'   => $request->boolean('is_active', true),
            'expires_at'  => $validated['expires_at'] ?? null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement published successfully.',
            'data'    => $announcement,
        ], 201);
    }

    /**
     * Show announcement detail.
     */
    public function show($id): JsonResponse
    {
        $announcement = Announcement::with('admin:id,name,email')->find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $announcement,
        ]);
    }

    /**
     * Update existing announcement.
     */
    public function update($id, AdminAnnouncementRequest $request): JsonResponse
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        $validated = $request->validated();
        if (isset($validated['target_audience']) && !isset($validated['target_role'])) {
            $validated['target_role'] = $validated['target_audience'];
        }

        $announcement->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement updated successfully.',
            'data'    => $announcement,
        ]);
    }

    /**
     * Delete announcement.
     */
    public function destroy($id): JsonResponse
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        $announcement->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement deleted successfully.',
        ]);
    }
}
