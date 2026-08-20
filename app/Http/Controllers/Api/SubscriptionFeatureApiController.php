<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionFeatureResource;
use App\Models\SubscriptionFeature;
use Illuminate\Http\JsonResponse;

class SubscriptionFeatureApiController extends Controller
{
    /**
     * Fetch all subscription features / permissions.
     */
    public function getFeatures(): JsonResponse
    {
        $features = SubscriptionFeature::orderBy('id', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Subscription permissions fetched successfully',
            'features' => SubscriptionFeatureResource::collection($features),
        ], 200);
    }
}
