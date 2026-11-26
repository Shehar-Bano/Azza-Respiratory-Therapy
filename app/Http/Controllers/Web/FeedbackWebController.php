<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackWebController extends Controller
{
    /**
     * Display listing of customer feedbacks with search, status filter, and pagination.
     */
    public function index(Request $request): View
    {
        $query = UserFeedback::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 30, 50, 100])) {
            $perPage = 10;
        }

        $feedbacks = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('admin.feedbacks.index', [
            'feedbacks' => $feedbacks,
            'search' => $request->input('search', ''),
            'selectedStatus' => $request->input('status', ''),
            'perPage' => $perPage,
        ]);
    }

    /**
     * Update feedback status (pending, reviewed, resolved).
     */
    public function updateStatus(Request $request, UserFeedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,reviewed,resolved'],
        ]);

        $feedback->update(['status' => $validated['status']]);

        return redirect()
            ->route('admin.feedbacks.index')
            ->with('success', 'Feedback status updated successfully.');
    }

    /**
     * Delete feedback record.
     */
    public function destroy(UserFeedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return redirect()
            ->route('admin.feedbacks.index')
            ->with('success', 'Feedback deleted successfully.');
    }
}
