<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\Petugas;
use App\Models\User;

class AppNotificationService
{
    public static function toAdmins(string $title, ?string $message = null, ?string $url = null): void
    {
        $ids = Admin::pluck('id')->all();
        self::sendMany('admin', $ids, $title, $message, $url);
    }

    public static function toPetugas(string $title, ?string $message = null, ?string $url = null): void
    {
        $ids = Petugas::pluck('id')->all();
        self::sendMany('petugas', $ids, $title, $message, $url);
    }

    public static function toUser(int $userId, string $title, ?string $message = null, ?string $url = null): void
    {
        self::sendMany('user', [$userId], $title, $message, $url);
    }

    public static function toAllStaff(string $title, ?string $message = null, ?string $url = null): void
    {
        self::toAdmins($title, $message, $url);
        self::toPetugas($title, $message, $url);
    }

    private static function sendMany(string $recipientType, array $ids, string $title, ?string $message, ?string $url): void
    {
        if (empty($ids)) {
            return;
        }

        $rows = [];
        $now = now();
        foreach ($ids as $id) {
            $rows[] = [
                'recipient_type' => $recipientType,
                'recipient_id' => $id,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        AppNotification::insert($rows);

        // Keep notification list short per recipient: only 3 latest rows.
        foreach ($ids as $id) {
            self::pruneRecipient($recipientType, (int) $id, 3);
        }
    }

    private static function pruneRecipient(string $recipientType, int $recipientId, int $keep): void
    {
        $keepIds = AppNotification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->latest('id')
            ->limit($keep)
            ->pluck('id');

        if ($keepIds->isEmpty()) {
            return;
        }

        AppNotification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }
}
