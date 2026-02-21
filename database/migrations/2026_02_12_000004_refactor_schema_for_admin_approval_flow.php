<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->foreignExists('users', 'role_id')) {
                    $table->dropForeign(['role_id']);
                }
                $table->dropColumn('role_id');
            });
        }

        if (Schema::hasTable('books') && Schema::hasColumn('books', 'petugas_id')) {
            Schema::table('books', function (Blueprint $table) {
                if ($this->foreignExists('books', 'petugas_id')) {
                    $table->dropForeign(['petugas_id']);
                }
                $table->dropColumn('petugas_id');
            });
        }

        if (Schema::hasTable('role')) {
            Schema::drop('role');
        }

        if (Schema::hasTable('wishlists') && !Schema::hasTable('koleksi_buku')) {
            Schema::rename('wishlists', 'koleksi_buku');
        }

        if (Schema::hasTable('reviews') && !Schema::hasTable('ulasan_buku')) {
            Schema::rename('reviews', 'ulasan_buku');
        }

        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
    }

    public function down(): void
    {
        if (Schema::hasTable('koleksi_buku') && !Schema::hasTable('wishlists')) {
            Schema::rename('koleksi_buku', 'wishlists');
        }

        if (Schema::hasTable('ulasan_buku') && !Schema::hasTable('reviews')) {
            Schema::rename('ulasan_buku', 'reviews');
        }
    }

    private function foreignExists(string $table, string $column): bool
    {
        $db = DB::getDatabaseName();
        $result = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$db, $table, $column]
        );

        return count($result) > 0;
    }
};
