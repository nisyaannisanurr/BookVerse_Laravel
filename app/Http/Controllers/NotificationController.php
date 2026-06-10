<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notifikasi::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        $id = (int) $request->id;
        if ($id > 0) {
            Notifikasi::where('id', $id)->where('user_id', auth()->id())->update(['is_read' => 1]);
        }

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->check()
            ? Notifikasi::where('user_id', auth()->id())->unread()->count()
            : 0;

        return response()->json(['count' => $count]);
    }

    public function markAllRead(Request $request)
    {
        Notifikasi::where('user_id', auth()->id())->unread()->update(['is_read' => 1]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('/notifications');
    }
}
