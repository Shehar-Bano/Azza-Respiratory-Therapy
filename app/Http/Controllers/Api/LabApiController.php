<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LabApiController extends Controller
{
    /**
     * Get labs history and latest lab record for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $labs = UserLab::where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 20));

        $latestLab = UserLab::where('user_id', $userId)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Labs retrieved successfully.',
            'data' => [
                'latest_lab' => $latestLab,
                'history' => $labs,
            ],
        ], 200);
    }

    /**
     * Get the latest lab record for authenticated user.
     */
    public function latest(Request $request): JsonResponse
    {
        $lab = UserLab::where('user_id', $request->user()->id)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => $lab ? 'Latest lab record retrieved.' : 'No lab records found.',
            'data' => [
                'lab' => $lab,
            ],
        ], 200);
    }

    /**
     * Save / record new lab values for authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'creatine' => ['nullable', 'numeric'],
            'sodium' => ['nullable', 'numeric'],
            'potassium' => ['nullable', 'numeric'],
            'hemoglobin' => ['nullable', 'numeric'],
            'lactate' => ['nullable', 'numeric'],
            'glucose' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lab = UserLab::create([
            'user_id' => $request->user()->id,
            'creatine' => $request->input('creatine'),
            'sodium' => $request->input('sodium'),
            'potassium' => $request->input('potassium'),
            'hemoglobin' => $request->input('hemoglobin'),
            'lactate' => $request->input('lactate'),
            'glucose' => $request->input('glucose'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Labs saved successfully.',
            'data' => [
                'lab' => $lab,
            ],
        ], 201);
    }
}
