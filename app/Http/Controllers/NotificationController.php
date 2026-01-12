<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getLatest()
    {
        $notifications = Notification::where('usuario_id', Auth::id())
            ->where('leido', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('usuario_id', Auth::id())->findOrFail($id);
        $notification->update(['leido' => true]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('usuario_id', Auth::id())->update(['leido' => true]);
        return response()->json(['success' => true]);
    }
}
