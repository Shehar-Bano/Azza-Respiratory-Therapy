<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    /**
     * Save subscription transaction and activate plan for user.
     */
    public function saveTransaction(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required',
            'plan_id' => 'required',
            'amount' => 'nullable',
            'currency' => 'nullable',
            'transaction_reference' => 'nullable',
            'cart_id' => 'nullable',
            'payment_gateway' => 'nullable',
            'payment_method' => 'nullable',
            'card_brand' => 'nullable',
            'card_first_six' => 'nullable',
            'card_last_four' => 'nullable',
            'payment_status' => 'nullable',
            'customer_name' => 'nullable',
            'customer_email' => 'nullable',
            'customer_phone' => 'nullable',
            'gateway_response' => 'nullable',
        ]);

        // Check if user exists in database
        $existingUser = User::find($request->user_id);
        $userId = $existingUser ? $existingUser->id : null;

        $plan = SubscriptionPlan::where('plan_id', (string) $request->plan_id)->first();
        $planTitle = $plan ? $plan->title : ('Subscription Plan ' . $request->plan_id);
        $durationDays = $plan && $plan->duration_days > 0 ? (int) $plan->duration_days : 30;

        $startedAt = Carbon::now();
        $expiresAt = $startedAt->copy()->addDays($durationDays);

        // Deactivate previous active subscriptions for this user if user exists
        if ($userId) {
            SubscriptionTransaction::where('user_id', $userId)
                ->where('status', 'active')
                ->update(['status' => 'suspended']);
        }

        $transaction = SubscriptionTransaction::create([
            'user_id' => $userId,
            'plan_id' => (string) $request->plan_id,
            'cart_id' => $request->cart_id,
            'transaction_reference' => $request->transaction_reference,
            'amount' => (string) $request->amount,
            'currency' => $request->currency ?? 'SAR',
            'payment_gateway' => $request->payment_gateway ?? 'PayTabs',
            'payment_method' => $request->payment_method ?? 'CreditCard',
            'card_brand' => $request->card_brand,
            'card_first_six' => $request->card_first_six,
            'card_last_four' => $request->card_last_four,
            'payment_status' => $request->payment_status ?? 'success',
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'gateway_response' => $request->gateway_response,
            'status' => 'active',
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
        ]);

        $remainingDays = (int) max(0, Carbon::now()->diffInDays($expiresAt, false));

        return response()->json([
            'status' => true,
            'message' => 'Subscription activated successfully',
            'subscription' => [
                'user_id' => (int) $request->user_id,
                'plan_id' => (string) $transaction->plan_id,
                'plan_title' => $planTitle,
                'transaction_ref' => $transaction->transaction_reference,
                'started_at' => $transaction->started_at ? $transaction->started_at->format('Y-m-d H:i:s') : null,
                'expires_at' => $transaction->expires_at ? $transaction->expires_at->format('Y-m-d H:i:s') : null,
                'remaining_days' => $remainingDays,
                'is_active' => $transaction->status === 'active' && $expiresAt->isFuture(),
            ],
        ], 200);
    }

    /**
     * Get user subscription status, active plan, and feature permissions.
     */
    public function getSubscriptionStatus(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? ($request->user() ? $request->user()->id : null);

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $now = Carbon::now();

        // Get latest active subscription transaction for user
        $sub = SubscriptionTransaction::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();

        $isActive = false;
        $isExpired = false;
        $activePlanId = "0";
        $planTitle = "Free Plan";
        $startedAt = null;
        $expiresAt = null;
        $daysRemaining = 0;
        $statusText = "inactive";

        $calculatorUnlocked = false;
        $articlesUnlocked = false;
        $cardsUnlocked = false;
        $classesUnlocked = false;

        if ($sub) {
            $startedAt = $sub->started_at;
            $expiresAt = $sub->expires_at;

            if ($expiresAt && $expiresAt->isPast()) {
                $isExpired = true;
                $statusText = "expired";
            } else {
                $isActive = true;
                $statusText = "active";
                $activePlanId = (string) $sub->plan_id;
                $planTitle = $sub->plan ? $sub->plan->title : ('Plan ' . $sub->plan_id);

                if ($expiresAt) {
                    $daysRemaining = (int) max(0, $now->diffInDays($expiresAt, false));
                }

                // Dynamically fetch feature slugs from plan's feature_ids
                $featureIds = is_array($sub->plan ? $sub->plan->feature_ids : null) ? $sub->plan->feature_ids : [];
                $featureSlugs = [];
                if (!empty($featureIds)) {
                    $featureSlugs = \App\Models\SubscriptionFeature::whereIn('id', $featureIds)->pluck('slug')->toArray();
                } else {
                    if ($activePlanId === "1") {
                        $featureSlugs = ['calculator_unlocked', 'articles_unlocked', 'cards_unlocked'];
                    } elseif ($activePlanId === "2") {
                        $featureSlugs = ['calculator_unlocked', 'articles_unlocked', 'cards_unlocked', 'classes_unlocked'];
                    }
                }

                $calculatorUnlocked = in_array('calculator_unlocked', $featureSlugs);
                $articlesUnlocked = in_array('articles_unlocked', $featureSlugs);
                $cardsUnlocked = in_array('cards_unlocked', $featureSlugs);
                $classesUnlocked = in_array('classes_unlocked', $featureSlugs);
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'user_id' => (int) $userId,
                'active_plan_id' => $activePlanId,
                'plan_title' => $planTitle,
                'subscription_status' => $statusText,
                'started_at' => $startedAt ? $startedAt->toISOString() : null,
                'expires_at' => $expiresAt ? $expiresAt->toISOString() : null,
                'days_remaining' => $daysRemaining,
                'is_expired' => $isExpired,
                'permissions' => [
                    'calculator_unlocked' => $calculatorUnlocked,
                    'articles_unlocked' => $articlesUnlocked,
                    'cards_unlocked' => $cardsUnlocked,
                    'classes_unlocked' => $classesUnlocked,
                ],
            ],
        ], 200);
    }

    /**
     * Get payment transaction history for user.
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? ($request->user() ? $request->user()->id : null);

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User ID is required',
                'history' => [],
            ], 400);
        }

        $transactions = SubscriptionTransaction::with('plan')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $history = $transactions->map(function ($sub) {
            $planTitle = $sub->plan ? $sub->plan->title : ('Subscription Plan ' . $sub->plan_id);

            // Format amount (if numeric, convert 1999 to 19.99 SAR or keep decimal format)
            $rawAmount = (string) $sub->amount;
            $formattedAmount = $rawAmount;
            if (is_numeric($rawAmount)) {
                $floatVal = (float) $rawAmount;
                if ($floatVal > 100 && strpos($rawAmount, '.') === false) {
                    $formattedAmount = number_format($floatVal / 100, 2);
                } else {
                    $formattedAmount = number_format($floatVal, 2);
                }
            }
            $formattedAmountStr = trim($formattedAmount . ' ' . ($sub->currency ?? 'SAR'));

            // Format masked payment method e.g. "VISA **** 0002"
            $brand = !empty($sub->card_brand) ? strtoupper($sub->card_brand) : (!empty($sub->payment_method) ? $sub->payment_method : 'CreditCard');
            $paymentMethodStr = $brand;
            if (!empty($sub->card_last_four)) {
                $paymentMethodStr .= ' **** ' . $sub->card_last_four;
            }

            return [
                'transaction_id' => (int) $sub->id,
                'transaction_ref' => $sub->transaction_reference ?? $sub->cart_id,
                'plan_title' => $planTitle,
                'amount' => $rawAmount,
                'formatted_amount' => $formattedAmountStr,
                'payment_method' => $paymentMethodStr,
                'payment_status' => $sub->payment_status ?? 'success',
                'date' => $sub->created_at ? $sub->created_at->format('Y-m-d H:i:s') : null,
                'valid_until' => $sub->expires_at ? $sub->expires_at->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Payment history retrieved',
            'history' => $history,
        ], 200);
    }
}
