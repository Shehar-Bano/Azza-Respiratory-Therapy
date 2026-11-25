<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserVital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VitalApiController extends Controller
{
    /**
     * Get vitals history and latest vital record for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $vitals = UserVital::where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 20));

        $latestVital = UserVital::where('user_id', $userId)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Vitals retrieved successfully.',
            'data' => [
                'latest_vital' => $latestVital,
                'history' => $vitals,
            ],
        ], 200);
    }

    /**
     * Get the latest vital record for authenticated user.
     */
    public function latest(Request $request): JsonResponse
    {
        $vital = UserVital::where('user_id', $request->user()->id)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => $vital ? 'Latest vital record retrieved.' : 'No vital records found.',
            'data' => [
                'vital' => $vital,
            ],
        ], 200);
    }

    /**
     * Save / record new vitals for authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sbp' => ['nullable', 'numeric'],
            'dbp' => ['nullable', 'numeric'],
            'hr' => ['nullable', 'numeric'],
            'rr' => ['nullable', 'numeric'],
            'spo2' => ['nullable', 'numeric'],
            'temp' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vital = UserVital::create([
            'user_id' => $request->user()->id,
            'sbp' => $request->input('sbp'),
            'dbp' => $request->input('dbp'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'spo2' => $request->input('spo2'),
            'temp' => $request->input('temp'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vitals saved successfully.',
            'data' => [
                'vital' => $vital,
            ],
        ], 201);
    }
}
