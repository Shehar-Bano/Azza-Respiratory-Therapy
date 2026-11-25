<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SupportContact;
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
        $contact = SupportContact::first();
        $defaults = SupportContact::defaultValues();

        return response()->json([
            'status' => true,
            'data' => [
                'support_email' => $contact ? $contact->support_email : $defaults['support_email'],
                'support_phone' => $contact ? $contact->support_phone : $defaults['support_phone'],
                'working_hours' => $contact ? $contact->working_hours : $defaults['working_hours'],
                'support_options' => ($contact && is_array($contact->support_options))
                    ? $contact->support_options
                    : $defaults['support_options'],
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
