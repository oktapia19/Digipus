<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AppNotificationController extends Controller
{
    public function go(AppNotification $notification): RedirectResponse
    {
        [$type, $id] = $this->recipientContext();

        if (!$type || !$id) {
            abort(403);
        }

        if ($notification->recipient_type !== $type || (int) $notification->recipient_id !== (int) $id) {
            abort(403);
        }

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        if ($type === 'user') {
            return back();
        }

        return redirect($notification->url ?: url()->previous());
    }

    public function readAll(): RedirectResponse
    {
        [$type, $id] = $this->recipientContext();

        if ($type && $id) {
            AppNotification::where('recipient_type', $type)
                ->where('recipient_id', $id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return back();
    }

    public function destroy(AppNotification $notification): RedirectResponse
    {
        [$type, $id] = $this->recipientContext();

        if (!$type || !$id) {
            abort(403);
        }

        if ($notification->recipient_type !== $type || (int) $notification->recipient_id !== (int) $id) {
            abort(403);
        }

        $notification->delete();

        return back();
    }

    public function destroyAll(): RedirectResponse
    {
        [$type, $id] = $this->recipientContext();

        if (!$type || !$id) {
            abort(403);
        }

        AppNotification::where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->delete();

        return back();
    }

    public function read(Request $request, AppNotification $notification): JsonResponse
    {
        [$type, $id] = $this->recipientContext();

        if (!$type || !$id) {
            abort(403);
        }

        if ($notification->recipient_type !== $type || (int) $notification->recipient_id !== (int) $id) {
            abort(403);
        }

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        $unreadCount = AppNotification::where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function destroyAllAjax(Request $request): JsonResponse
    {
        [$type, $id] = $this->recipientContext();

        if (!$type || !$id) {
            abort(403);
        }

        AppNotification::where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    private function recipientContext(): array
    {
        if (auth('admin')->check()) {
            return ['admin', auth('admin')->id()];
        }
        if (auth('petugas')->check()) {
            return ['petugas', auth('petugas')->id()];
        }
        if (auth()->check()) {
            return ['user', auth()->id()];
        }

        return [null, null];
    }
}
