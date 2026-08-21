<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserWebController extends Controller
{
    /**
     * Display listing of users with global search, column sorting, and dynamic pagination.
     */
    public function index(Request $request): View
    {
        $query = User::with(['activeSubscription.plan']);

        // Global Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'name', 'email', 'role', 'status', 'created_at'];
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

        $users = $query->paginate($perPage)->withQueryString();
        $plans = SubscriptionPlan::orderBy('id', 'asc')->get();

        return view('admin.users.index', [
            'users' => $users,
            'plans' => $plans,
            'search' => $request->input('search', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Store a newly created user and optionally assign subscription.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:user,admin',
            'allow_subscription' => 'nullable',
            'plan_id' => 'required_if:allow_subscription,1,on,true|nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|numeric|min:1',
        ]);

        $rawPassword = $request->filled('password') ? $request->password : Str::random(12);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($rawPassword),
            'role' => $request->role ?? 'user',
            'status' => 'active',
        ];

        $user = User::create($userData);

        $hasSubscription = false;

        $allowSub = $request->has('allow_subscription') && ($request->allow_subscription == '1' || $request->allow_subscription == 'on' || $request->allow_subscription == 'true' || $request->allow_subscription === true);

        if ($allowSub && $request->filled('plan_id')) {
            $plan = SubscriptionPlan::where('plan_id', (string) $request->plan_id)->first()
                ?? SubscriptionPlan::find($request->plan_id);

            if ($plan) {
                $durationDays = (int) ($request->input('duration_days') ?: ($plan->duration_days > 0 ? $plan->duration_days : 30));
                $amount = $request->filled('amount') ? (string) $request->amount : (string) ($plan->price_sar ?? $plan->price ?? '0.00');
                $startedAt = Carbon::now();
                $expiresAt = $startedAt->copy()->addDays($durationDays);

                SubscriptionTransaction::create([
                    'user_id' => $user->id,
                    'plan_id' => (string) $plan->plan_id,
                    'cart_id' => 'CASH-GRANT-' . strtoupper(Str::random(6)),
                    'transaction_reference' => 'CASH-' . time() . '-' . $user->id,
                    'amount' => $amount,
                    'currency' => $plan->currency_sar ?? $plan->currency ?? 'SAR',
                    'payment_gateway' => 'Cash',
                    'payment_method' => 'Cash',
                    'payment_status' => 'success',
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone ?? null,
                    'status' => 'active',
                    'started_at' => $startedAt,
                    'expires_at' => $expiresAt,
                ]);

                $hasSubscription = true;
            }
        }

        $message = "User '{$user->name}' created successfully" . ($hasSubscription ? " with an active subscription." : ".");
        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Create or Update subscription for a specific user.
     */
    public function updateSubscription(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,suspended',
            'duration_days' => 'nullable|integer|min:1',
        ]);

        $plan = SubscriptionPlan::where('plan_id', (string) $request->plan_id)->first()
            ?? SubscriptionPlan::find($request->plan_id);

        $activeSub = SubscriptionTransaction::where('user_id', $user->id)
            ->latest()
            ->first();

        $durationDays = (int) ($request->input('duration_days') ?: ($plan && $plan->duration_days > 0 ? $plan->duration_days : 30));
        $startedAt = $activeSub ? ($activeSub->started_at ?? Carbon::now()) : Carbon::now();
        $expiresAt = Carbon::now()->addDays($durationDays);

        if ($activeSub) {
            $activeSub->update([
                'plan_id' => $plan ? (string) $plan->plan_id : (string) $request->plan_id,
                'amount' => (string) $request->amount,
                'status' => $request->status,
                'expires_at' => $expiresAt,
            ]);
        } else {
            SubscriptionTransaction::create([
                'user_id' => $user->id,
                'plan_id' => $plan ? (string) $plan->plan_id : (string) $request->plan_id,
                'cart_id' => 'CASH-GRANT-' . strtoupper(Str::random(6)),
                'transaction_reference' => 'CASH-' . time() . '-' . $user->id,
                'amount' => (string) $request->amount,
                'currency' => $plan->currency_sar ?? $plan->currency ?? 'SAR',
                'payment_gateway' => 'Cash',
                'payment_method' => 'Cash',
                'payment_status' => 'success',
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'status' => $request->status,
                'started_at' => $startedAt,
                'expires_at' => $expiresAt,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "Subscription updated successfully for user #{$user->id} ({$user->name}).");
    }

    /**
     * Update specified user status (active / suspended).
     */
    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:active,suspended,inactive',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', "User #{$user->id} ({$user->name}) status updated to '{$user->status}'.");
    }
}
