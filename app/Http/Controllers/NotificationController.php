<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json(['data' => $notifications]);
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)->update(['is_read' => true]);
        return response()->json(['message' => 'Notifikasi sudah dibaca']);
    }
}
