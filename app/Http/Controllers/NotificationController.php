<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the authenticated user's notification center.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Mark a single notification as read and optionally navigate to its target resource.
     */
    public function markAsRead(Request $request, AppNotification $notification): RedirectResponse|JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
