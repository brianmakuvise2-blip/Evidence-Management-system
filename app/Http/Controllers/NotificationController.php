<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        // Ensure the notification belongs to the current user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(DatabaseNotification $notification)
    {
        // Ensure the notification belongs to the current user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Get unread notifications count and latest notifications for AJAX.
     */
    public function getUnread(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        $latestNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->data['action_url'] ?? null,
                    'hash_summary' => $notification->data['hash_summary'] ?? null,
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latestNotifications,
        ]);
    }
}