<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Models\ClinicalCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CardApiController extends Controller
{
    /**
     * Get list of clinical reference cards with multiple images with pagination.
     */
    public function getCards(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));

        $cards = ClinicalCard::with('images')->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Cards fetched successfully',
            'card' => CardResource::collection($cards->items()),
            'pagination' => [
                'total' => $cards->total(),
                'count' => $cards->count(),
                'per_page' => $cards->perPage(),
                'current_page' => $cards->currentPage(),
                'total_pages' => $cards->lastPage(),
                'has_more_pages' => $cards->hasMorePages(),
            ],
        ], 200);
    }
}
