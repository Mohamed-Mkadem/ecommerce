<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class NotificationController extends Controller
{
    public function index(Request $request)
    {

        $status = $request->input('status', 'all');
        $query = $request->user()->notifications();

        if ($status === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($status === 'unread') {
            $query->whereNull('read_at');
        }

        return Inertia::render('Admin/Notifications', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function markAsRead(Request $request, $notification_id)
    {
        $notification = $request->user()->unreadNotifications()->find($notification_id);
        if ($notification) {
            $notification->markAsRead();
            return ['success' => true];
        }
    }
    public function markAllAsRead(Request $request)
    {

        $request->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    public function clean()
    {
        $exitCode = Artisan::call('notifications:prune');

        return redirect()->back()->with(
            $exitCode === 0 ? 'success' : 'error',
            $exitCode === 0 ? 'Notifications cleaned successfully.' : 'Failed to clean notifications.'
        );
    }

    public function getNotifications(User $user)
    {
        return $user->notifications()->take(4)->get();
    }
}
