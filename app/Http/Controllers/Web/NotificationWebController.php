<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use App\Notifications\CustomPushNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationWebController extends Controller
{
    /**
     * Show notification sending form and history list.
     */
    public function index(Request $request): View
    {
        $users = User::orderBy('name')->get();

        $notifications = AppNotification::with('user')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.notifications.index', compact('users', 'notifications'));
    }

    /**
     * Send notification to selected users or all users.
     */
    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'user_ids' => ['required_without:select_all', 'array'],
            'user_ids.*' => ['nullable', 'string'],
        ]);

        $selectAll = $request->boolean('select_all') || (is_array($request->user_ids) && in_array('all', $request->user_ids));

        if ($selectAll) {
            $targetUsers = User::all();
        } else {
            $userIds = array_filter((array) $request->user_ids, function ($val) {
                return !empty($val) && $val !== 'all';
            });
            $targetUsers = User::whereIn('id', $userIds)->get();
        }

        if ($targetUsers->isEmpty()) {
            return redirect()->back()->with('error', 'No valid users selected to send notification.');
        }

        $title = $request->input('title');
        $message = $request->input('message');
        $type = $request->input('type') ?: 'admin_broadcast';

        $count = 0;
        foreach ($targetUsers as $user) {
            $user->notify(new CustomPushNotification(
                title: $title,
                message: $message,
                type: $type,
                data: [
                    'sent_by' => auth()->user()->name ?? 'Admin',
                    'sent_at' => now()->toIso8601String(),
                ]
            ));
            $count++;
        }

        return redirect()->route('admin.notifications.index')->with(
            'success',
            "Notification successfully queued and dispatched to {$count} user(s)."
        );
    }
}
