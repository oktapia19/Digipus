<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role') && !Schema::hasTable('roles')) {
            Schema::rename('role', 'roles');
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        $now = now();
        DB::table('roles')->upsert(
            [
                ['name' => 'admin', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'petugas', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'user', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['name'],
            ['updated_at']
        );

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $petugasRoleId = DB::table('roles')->where('name', 'petugas')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            });
        }

        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'role_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            });
        }

        if (Schema::hasTable('petugas') && !Schema::hasColumn('petugas', 'role_id')) {
            Schema::table('petugas', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            });
        }

        if ($userRoleId) {
            DB::table('users')
                ->whereNull('role_id')
                ->update(['role_id' => $userRoleId]);
        }

        if ($adminRoleId) {
            DB::table('admins')
                ->whereNull('role_id')
                ->update(['role_id' => $adminRoleId]);
        }

        if ($petugasRoleId) {
            DB::table('petugas')
                ->whereNull('role_id')
                ->update(['role_id' => $petugasRoleId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'role_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }

        if (Schema::hasTable('petugas') && Schema::hasColumn('petugas', 'role_id')) {
            Schema::table('petugas', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }

        Schema::dropIfExists('roles');
    }
};
