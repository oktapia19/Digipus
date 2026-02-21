<?php

namespace App\Http\Middleware;

use App\Models\AppNotification;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareAppNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        $recipientType = null;
        $recipientId = null;

        if ($request->routeIs('admin.*') && auth('admin')->check()) {
            $recipientType = 'admin';
            $recipientId = auth('admin')->id();
        } elseif ($request->routeIs('petugas.*') && auth('petugas')->check()) {
            $recipientType = 'petugas';
            $recipientId = auth('petugas')->id();
        } elseif (auth()->check()) {
            $recipientType = 'user';
            $recipientId = auth()->id();
        } elseif (auth('admin')->check()) {
            $recipientType = 'admin';
            $recipientId = auth('admin')->id();
        } elseif (auth('petugas')->check()) {
            $recipientType = 'petugas';
            $recipientId = auth('petugas')->id();
        }

        if ($recipientType && $recipientId) {
            // Enforce max 3 notifications so old entries do not pile up.
            $keepIds = AppNotification::where('recipient_type', $recipientType)
                ->where('recipient_id', $recipientId)
                ->latest('id')
                ->limit(3)
                ->pluck('id');

            if ($keepIds->isNotEmpty()) {
                AppNotification::where('recipient_type', $recipientType)
                    ->where('recipient_id', $recipientId)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }

            $notifications = AppNotification::where('recipient_type', $recipientType)
                ->where('recipient_id', $recipientId)
                ->latest()
                ->limit(3)
                ->get();

            $unreadCount = AppNotification::where('recipient_type', $recipientType)
                ->where('recipient_id', $recipientId)
                ->where('is_read', false)
                ->count();

            view()->share('appNotifications', $notifications);
            view()->share('appNotificationUnreadCount', $unreadCount);
        } else {
            view()->share('appNotifications', collect());
            view()->share('appNotificationUnreadCount', 0);
        }

        return $next($request);
    }
}
