<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPlanWebController extends Controller
{
    /**
     * Display listing of subscription plans.
     */
    public function index(Request $request): View
    {
        $query = SubscriptionPlan::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('plan_id', 'like', "%{$search}%")
                  ->orWhere('access', 'like', "%{$search}%");
            });
        }

        $plans = $query->orderBy('id', 'asc')->get();

        return view('admin.plans.index', [
            'plans' => $plans,
            'search' => $request->input('search', ''),
        ]);
    }

    /**
     * Update specified subscription plan.
     */
    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price_usd' => 'required|string|max:50',
            'price_sar' => 'required|string|max:50',
            'duration_days' => 'required|integer|min:0',
            'access' => 'nullable|string',
            'features' => 'nullable|string', // comma separated or lines
        ]);

        $featuresArray = [];
        if ($request->filled('features')) {
            $rawFeatures = explode("\n", $request->input('features'));
            foreach ($rawFeatures as $f) {
                $trimmed = trim($f);
                if (!empty($trimmed)) {
                    $featuresArray[] = $trimmed;
                }
            }
        }

        $plan->update([
            'title' => $request->title,
            'price' => $request->price_usd,
            'price_usd' => $request->price_usd,
            'price_sar' => $request->price_sar,
            'duration_days' => $request->duration_days,
            'access' => $request->access,
            'features' => $featuresArray,
        ]);

        return redirect()->route('admin.plans.index')->with('success', 'Subscription plan updated successfully.');
    }
}
