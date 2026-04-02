<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Notifier
{
    public static function notify(User $user, string $type, string $message, ?string $link = null): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'link' => $link,
        ]);

        try {
            Mail::raw($message, function ($mail) use ($user, $type) {
                $mail->to($user->email)
                    ->subject("BorrowIT Notification: {$type}");
            });
        } catch (Throwable $e) {
            // Ignore email failures if mail server not configured.
        }
    }
}
