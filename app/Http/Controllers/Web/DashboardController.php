<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(): View
    {
        $subscriptionPlans = SubscriptionPlan::orderBy('id', 'asc')->get();

        $metrics = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
            'totalPlans' => $subscriptionPlans->count(),
            'recentUsers' => User::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', [
            'user' => Auth::user(),
            'metrics' => $metrics,
            'subscriptionPlans' => $subscriptionPlans,
        ]);
    }
}
