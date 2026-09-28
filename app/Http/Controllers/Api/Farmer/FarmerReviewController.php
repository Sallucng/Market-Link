<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\FarmerReviewReplyRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerReviewController extends Controller
{
    /**
     * View customer reviews for this farmer's products and orders.
     */
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $farmerId = $request->user()->id;

        $query = Review::where('farmer_id', $farmerId)
            ->where('is_moderated', false)
            ->with(['customer:id,name,email', 'order:id,pickup_date,pickup_time', 'product:id,name,image']);

        if ($request->filled('rating')) {
            $query->where('rating', (int)$request->rating);
        }

        if ($request->filled('has_reply')) {
            $hasReply = filter_var($request->has_reply, FILTER_VALIDATE_BOOLEAN);
            if ($hasReply) {
                $query->whereNotNull('farmer_reply');
            } else {
                $query->whereNull('farmer_reply');
            }
        }

        $reviews = $query->latest()->paginate($request->input('per_page', 15));

        $averageRating = Review::where('farmer_id', $farmerId)
            ->where('is_moderated', false)
            ->avg('rating');

        $totalReviews = Review::where('farmer_id', $farmerId)
            ->where('is_moderated', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_reviews' => $totalReviews,
                'average_rating' => round((float)$averageRating, 1),
            ],
            'data' => $reviews,
        ]);
    }

    /**
     * Submit or update a farmer reply to a customer review.
     */
    public function reply($id, FarmerReviewReplyRequest $request): JsonResponse
    {
        $farmerId = $request->user()->id;

        $review = Review::where('farmer_id', $farmerId)->find($id);

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found.',
            ], 404);
        }

        $review->farmer_reply = $request->validated('farmer_reply');
        $review->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Response submitted successfully.',
            'data' => $review->fresh(['customer:id,name', 'order:id', 'product:id,name']),
        ]);
    }
}
