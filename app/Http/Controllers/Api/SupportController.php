<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\UserFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    /**
     * Get official support contact details.
     */
    public function getSupportContact(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'support_email' => config('mail.support_email', 'support@azzatherapy.com'),
                'support_phone' => '+966 11 000 0000',
                'working_hours' => 'Sun - Thu: 9:00 AM - 6:00 PM (AST)',
                'support_options' => [
                    'Direct Email Support',
                    'In-App Feedback & Suggestions',
                    'Frequently Asked Clinical Questions (FAQs)',
                ],
            ],
        ], 200);
    }

    /**
     * Get categorized clinical and account FAQs with optional category & keyword filtering.
     */
    public function getFaqs(Request $request): JsonResponse
    {
        $query = Faq::query();

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('search')) {
            $keyword = '%' . trim((string) $request->query('search')) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('question', 'like', $keyword)
                    ->orWhere('answer', 'like', $keyword)
                    ->orWhere('category', 'like', $keyword);
            });
        }

        $faqs = $query->orderBy('id')->get(['id', 'category', 'question', 'answer']);

        return response()->json([
            'status' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $faqs,
        ], 200);
    }

    /**
     * Submit user feedback or clinical inquiry.
     */
    public function submitFeedback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        UserFeedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feedback submitted successfully. Thank you for helping us improve Azza Respiratory Therapy.',
        ], 200);
    }
}
