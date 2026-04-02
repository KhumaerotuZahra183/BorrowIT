<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function requireAuth(Request $request)
    {
        $userId = $request->session()->get('auth_user_id');
        if (!$userId) {
            return [null, redirect()->route('login')];
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->forget('auth_user_id');
            return [null, redirect()->route('login')];
        }

        return [$user, null];
    }

    public function index(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $notifications = Notification::query()
            ->where('user_id', $authUser->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('notifications.index', [
            'user' => $authUser,
            'notifications' => $notifications,
        ]);
    }

    public function readAll(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        Notification::where('user_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index');
    }
}
