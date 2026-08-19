<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Models\ClinicalCard;
use Illuminate\Http\JsonResponse;

class CardApiController extends Controller
{
    /**
     * Get list of clinical reference cards.
     */
    public function getCards(): JsonResponse
    {
        $cards = ClinicalCard::latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Cards fetched successfully',
            'card' => CardResource::collection($cards),
        ], 200);
    }
}
