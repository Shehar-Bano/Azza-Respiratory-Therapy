<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLab;
use App\Models\UserVital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HealthRecordApiController extends Controller
{
    /**
     * Get combined latest vitals, labs, and basic user information.
     */
    public function getHealthData(Request $request): JsonResponse
    {
        $user = $request->user();

        $latestVital = UserVital::where('user_id', $user->id)
            ->latest('id')
            ->first();

        $latestLab = UserLab::where('user_id', $user->id)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Health data retrieved successfully.',
            'data' => [
                'basic_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'age' => $user->age,
                    'gender' => $user->gender,
                    'sex' => $user->gender,
                    'height' => $user->height,
                    'weight' => $user->weight,
                    'bmi' => $user->bmi,
                ],
                'vitals' => $latestVital,
                'labs' => $latestLab,
            ],
        ], 200);
    }

    /**
     * Save combined vitals, labs, and optionally update basic info in a single API call.
     */
    public function saveHealthData(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validation supporting both flat and nested parameters
        $validator = Validator::make($request->all(), [
            // Basic Info
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', 'string', 'max:20'],
            'sex' => ['nullable', 'string', 'max:20'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'bmi' => ['nullable', 'numeric', 'min:0'],

            // Vitals
            'sbp' => ['nullable', 'numeric'],
            'dbp' => ['nullable', 'numeric'],
            'hr' => ['nullable', 'numeric'],
            'rr' => ['nullable', 'numeric'],
            'spo2' => ['nullable', 'numeric'],
            'temp' => ['nullable', 'numeric'],

            // Labs
            'creatine' => ['nullable', 'numeric'],
            'sodium' => ['nullable', 'numeric'],
            'potassium' => ['nullable', 'numeric'],
            'hemoglobin' => ['nullable', 'numeric'],
            'lactate' => ['nullable', 'numeric'],
            'glucose' => ['nullable', 'numeric'],

            // Nested support
            'basic_info' => ['nullable', 'array'],
            'vitals' => ['nullable', 'array'],
            'labs' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $user) {
            // 1. Update Basic Information if provided
            $basicData = [];
            $age = $request->input('age') ?? $request->input('basic_info.age');
            if ($age !== null) $basicData['age'] = $age;

            $gender = $request->input('gender') ?? $request->input('sex') ?? $request->input('basic_info.gender') ?? $request->input('basic_info.sex');
            if ($gender !== null) $basicData['gender'] = $gender;

            $height = $request->input('height') ?? $request->input('basic_info.height');
            if ($height !== null) $basicData['height'] = $height;

            $weight = $request->input('weight') ?? $request->input('basic_info.weight');
            if ($weight !== null) $basicData['weight'] = $weight;

            $bmi = $request->input('bmi') ?? $request->input('basic_info.bmi');
            if ($bmi !== null) {
                $basicData['bmi'] = $bmi;
            } elseif (isset($basicData['height']) && isset($basicData['weight']) && (float)$basicData['height'] > 0) {
                $hMeters = (float)$basicData['height'] / 100;
                $basicData['bmi'] = round((float)$basicData['weight'] / ($hMeters * $hMeters), 2);
            }

            if (!empty($basicData)) {
                $user->update($basicData);
            }

            // 2. Save Vitals Record
            $sbp = $request->input('sbp') ?? $request->input('vitals.sbp');
            $dbp = $request->input('dbp') ?? $request->input('vitals.dbp');
            $hr = $request->input('hr') ?? $request->input('vitals.hr');
            $rr = $request->input('rr') ?? $request->input('vitals.rr');
            $spo2 = $request->input('spo2') ?? $request->input('vitals.spo2');
            $temp = $request->input('temp') ?? $request->input('vitals.temp');

            $hasVitalsData = $sbp !== null || $dbp !== null || $hr !== null || $rr !== null || $spo2 !== null || $temp !== null;

            $vital = null;
            if ($hasVitalsData) {
                $vital = UserVital::create([
                    'user_id' => $user->id,
                    'sbp' => $sbp,
                    'dbp' => $dbp,
                    'hr' => $hr,
                    'rr' => $rr,
                    'spo2' => $spo2,
                    'temp' => $temp,
                ]);
            } else {
                $vital = UserVital::where('user_id', $user->id)->latest('id')->first();
            }

            // 3. Save Labs Record
            $creatine = $request->input('creatine') ?? $request->input('labs.creatine');
            $sodium = $request->input('sodium') ?? $request->input('labs.sodium');
            $potassium = $request->input('potassium') ?? $request->input('labs.potassium');
            $hemoglobin = $request->input('hemoglobin') ?? $request->input('labs.hemoglobin');
            $lactate = $request->input('lactate') ?? $request->input('labs.lactate');
            $glucose = $request->input('glucose') ?? $request->input('labs.glucose');

            $hasLabsData = $creatine !== null || $sodium !== null || $potassium !== null || $hemoglobin !== null || $lactate !== null || $glucose !== null;

            $lab = null;
            if ($hasLabsData) {
                $lab = UserLab::create([
                    'user_id' => $user->id,
                    'creatine' => $creatine,
                    'sodium' => $sodium,
                    'potassium' => $potassium,
                    'hemoglobin' => $hemoglobin,
                    'lactate' => $lactate,
                    'glucose' => $glucose,
                ]);
            } else {
                $lab = UserLab::where('user_id', $user->id)->latest('id')->first();
            }

            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Vitals and Labs saved successfully.',
                'data' => [
                    'basic_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'age' => $user->age,
                        'gender' => $user->gender,
                        'height' => $user->height,
                        'weight' => $user->weight,
                        'bmi' => $user->bmi,
                    ],
                    'vitals' => $vital,
                    'labs' => $lab,
                ],
            ], 201);
        });
    }
}
