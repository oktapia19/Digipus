<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('books') || !Schema::hasColumn('books', 'status')) {
            return;
        }

        DB::statement(
            "ALTER TABLE books MODIFY COLUMN status ENUM('pending','approved','rejected','pending_delete','menunggu_konfirmasi','disetujui','ditolak','menunggu_hapus') NOT NULL DEFAULT 'pending'"
        );

        DB::table('books')->where('status', 'pending')->update(['status' => 'menunggu_konfirmasi']);
        DB::table('books')->where('status', 'approved')->update(['status' => 'disetujui']);
        DB::table('books')->where('status', 'rejected')->update(['status' => 'ditolak']);
        DB::table('books')->where('status', 'pending_delete')->update(['status' => 'menunggu_hapus']);

        DB::statement(
            "ALTER TABLE books MODIFY COLUMN status ENUM('menunggu_konfirmasi','disetujui','ditolak','menunggu_hapus') NOT NULL DEFAULT 'menunggu_konfirmasi'"
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('books') || !Schema::hasColumn('books', 'status')) {
            return;
        }

        DB::statement(
            "ALTER TABLE books MODIFY COLUMN status ENUM('pending','approved','rejected','pending_delete','menunggu_konfirmasi','disetujui','ditolak','menunggu_hapus') NOT NULL DEFAULT 'menunggu_konfirmasi'"
        );

        DB::table('books')->where('status', 'menunggu_konfirmasi')->update(['status' => 'pending']);
        DB::table('books')->where('status', 'disetujui')->update(['status' => 'approved']);
        DB::table('books')->where('status', 'ditolak')->update(['status' => 'rejected']);
        DB::table('books')->where('status', 'menunggu_hapus')->update(['status' => 'pending_delete']);

        DB::statement(
            "ALTER TABLE books MODIFY COLUMN status ENUM('pending','approved','rejected','pending_delete') NOT NULL DEFAULT 'pending'"
        );
    }
};
