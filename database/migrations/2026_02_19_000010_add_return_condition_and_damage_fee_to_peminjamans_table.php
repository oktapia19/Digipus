<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('kondisi_buku', 20)->nullable()->after('tanggal_pengembalian');
            $table->unsignedInteger('denda_tambahan')->default(0)->after('kondisi_buku');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn(['kondisi_buku', 'denda_tambahan']);
        });
    }
};

