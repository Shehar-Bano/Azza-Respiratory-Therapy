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
}
