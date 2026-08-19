<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        $metrics = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', [
            'user' => Auth::user(),
            'metrics' => $metrics,
        ]);
    }
}
