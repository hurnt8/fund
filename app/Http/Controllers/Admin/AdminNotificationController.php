<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function list()
    {
        $notifications = AdminNotification::where('admin_id', Auth::id())
            ->latest()
            ->limit(25)
            ->get()
            ->map(fn ($n) => [
                'id'    => $n->id,
                'icon'  => $n->icon,
                'type'  => $n->type,
                'title' => $n->title,
                'body'  => $n->body,
                'read'  => ! is_null($n->read_at),
                'time'  => $n->created_at->diffForHumans(),
                'url'   => $n->type === 'support' && isset($n->data['client_id'])
                           ? route('admin.support.show', $n->data['client_id'])
                           : null,
            ]);

        return response()->json(['notifications' => $notifications]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => AdminNotification::where('admin_id', Auth::id())
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function markRead(AdminNotification $notification)
    {
        abort_unless($notification->admin_id === Auth::id(), 403);
        $notification->update(['read_at' => now()]);
        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }

    public function markAllRead()
    {
        AdminNotification::where('admin_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }
}
