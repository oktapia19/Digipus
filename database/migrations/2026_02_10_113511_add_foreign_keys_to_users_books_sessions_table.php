<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            if (Schema::hasColumn('sessions', 'user_id')) {
                if (! $this->foreignExists('sessions', 'user_id')) {
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            if ($this->foreignExists('sessions', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });
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
