<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanApiController extends Controller
{
    /**
     * Get list of subscription plans.
     */
    public function getPlans(): JsonResponse
    {
        $plans = SubscriptionPlan::orderBy('id', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Subscription plans fetched successfully',
            'plans' => SubscriptionPlanResource::collection($plans),
        ], 200);
    }
}
