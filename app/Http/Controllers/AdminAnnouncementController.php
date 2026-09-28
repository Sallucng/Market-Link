<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    /**
     * List all announcements
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with('admin');

        // Optional filter by active status
        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                $request->query('is_active')
            );
        }

        $announcements = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($announcements, 200);
    }

    /**
     * Create a new announcement
     */
    public function store(
        StoreAnnouncementRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $validated['admin_id'] = $request->user()->id;

        $announcement = Announcement::create($validated);

        return response()->json([
            'status'       => 'success',
            'message'      => 'Announcement created successfully.',
            'announcement' => $announcement->load('admin'),
            'data'         => $announcement,
        ], 201);
    }

    /**
     * View a specific announcement
     */
    public function show($id): JsonResponse
    {
        $announcement = Announcement::with('admin')
            ->find($id);

        if (! $announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.'
            ], 404);
        }

        return response()->json([
            'status'       => 'success',
            'announcement' => $announcement,
            'data'         => $announcement,
        ], 200);
    }

    /**
     * Update an announcement
     */
    public function update(
        UpdateAnnouncementRequest $request,
        $id
    ): JsonResponse {
        $announcement = Announcement::find($id);

        if (! $announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.'
            ], 404);
        }

        $announcement->update($request->validated());

        return response()->json([
            'status'       => 'success',
            'message'      => 'Announcement updated successfully.',
            'announcement' => $announcement->load('admin'),
            'data'         => $announcement,
        ], 200);
    }

    /**
     * Delete an announcement
     */
    public function destroy($id): JsonResponse
    {
        $announcement = Announcement::find($id);

        if (! $announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.'
            ], 404);
        }

        $announcement->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement deleted successfully.',
            'data'    => null,
        ], 200);
    }
}
