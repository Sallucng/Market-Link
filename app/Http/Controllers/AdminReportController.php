<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * List all reports
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with('admin');

        // Optional filter by report type
        if ($request->filled('report_type')) {
            $query->where(
                'report_type',
                $request->query('report_type')
            );
        }

        $reports = $query
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        return response()->json($reports, 200);
    }

    /**
     * Create a new report
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['admin_id'] = $request->user()->id;

        $report = Report::create($validated);

        return response()->json([
            'message' => 'Report created successfully.',
            'report' => $report->load('admin'),
        ], 201);
    }

    /**
     * View a specific report
     */
    public function show($id): JsonResponse
    {
        $report = Report::with('admin')->find($id);

        if (! $report) {
            return response()->json([
                'message' => 'Report not found.'
            ], 404);
        }

        return response()->json([
            'report' => $report
        ], 200);
    }

    /**
     * Update a report
     */
    public function update(
        UpdateReportRequest $request,
        $id
    ): JsonResponse {
        $report = Report::find($id);

        if (! $report) {
            return response()->json([
                'message' => 'Report not found.'
            ], 404);
        }

        $report->update($request->validated());

        return response()->json([
            'message' => 'Report updated successfully.',
            'report' => $report->load('admin'),
        ], 200);
    }

    /**
     * Delete a report
     */
    public function destroy($id): JsonResponse
    {
        $report = Report::find($id);

        if (! $report) {
            return response()->json([
                'message' => 'Report not found.'
            ], 404);
        }

        $report->delete();

        return response()->json([
            'message' => 'Report deleted successfully.'
        ], 200);
    }
}
