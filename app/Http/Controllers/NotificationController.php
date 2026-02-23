<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getLatest()
    {
        // Fetch notifications up to 3 months ago instead of a fixed limit
        $notifications = auth()->user()->notifications()
            ->where('created_at', '>=', now()->subMonths(3))
            ->latest()
            ->get()
            ->map(function ($n) {
            return [
                'id' => $n->id,
                'mensaje' => $n->data['mensaje'] ?? 'Nueva notificación',
                'link' => $n->data['link'] ?? '#',
                'leido' => $n->read_at !== null,
                'created_at' => $n->created_at,
                'data' => $n->data // Include original data just in case
            ];
        });
        
        // Count unread
        $unreadCount = auth()->user()->unreadNotifications->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
}
