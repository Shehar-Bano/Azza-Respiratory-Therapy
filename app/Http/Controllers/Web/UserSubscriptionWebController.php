<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSubscriptionWebController extends Controller
{
    /**
     * Display listing of user subscriptions.
     */
    public function index(Request $request): View
    {
        $query = SubscriptionTransaction::with(['user', 'plan']);

        // Search by user name, email, transaction_reference, plan_id, or customer info
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('cart_id', 'like', "%{$search}%")
                  ->orWhere('plan_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status') && in_array($request->status, ['active', 'suspended'])) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'created_at', 'expires_at', 'status', 'amount'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // Dynamic Per Page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 30, 50, 100])) {
            $perPage = 10;
        }

        $subscriptions = $query->paginate($perPage)->withQueryString();
        $plans = SubscriptionPlan::orderBy('id', 'asc')->get();

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'plans' => $plans,
            'search' => $request->input('search', ''),
            'selectedStatus' => $request->input('status', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Update details for specified subscription transaction.
     */
    public function update(Request $request, SubscriptionTransaction $subscription): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,suspended',
            'duration_days' => 'nullable|integer|min:1',
        ]);

        $plan = SubscriptionPlan::where('plan_id', (string) $request->plan_id)
            ->orWhere('id', $request->plan_id)
            ->first();

        $durationDays = (int) ($request->input('duration_days') ?: ($plan && $plan->duration_days > 0 ? $plan->duration_days : 30));
        $startedAt = $subscription->started_at ?? Carbon::now();
        $expiresAt = Carbon::now()->addDays($durationDays);

        $subscription->update([
            'plan_id' => $plan ? (string) $plan->plan_id : (string) $request->plan_id,
            'amount' => (string) $request->amount,
            'status' => $request->status,
            'expires_at' => $expiresAt,
        ]);

        return redirect()->back()->with('success', "Subscription transaction #{$subscription->id} updated successfully.");
    }

    /**
     * Update status (Active / Suspend) for specified subscription transaction.
     */
    public function updateStatus(Request $request, SubscriptionTransaction $subscription): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:active,suspended',
        ]);

        $subscription->update([
            'status' => $request->status,
        ]);

        $statusLabel = ucfirst($request->status);
        return redirect()->back()->with('success', "Subscription status updated to {$statusLabel}.");
    }
}
