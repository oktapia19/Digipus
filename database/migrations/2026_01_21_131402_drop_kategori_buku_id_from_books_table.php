<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // kalau ada foreign key, drop dulu
            if (Schema::hasColumn('books', 'kategori_buku_id')) {
                $table->dropForeign(['kategori_buku_id']);
                $table->dropColumn('kategori_buku_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_buku_id')->nullable();

            $table->foreign('kategori_buku_id')
                  ->references('id')
                  ->on('kategori_buku')
                  ->onDelete('set null');
        });
    }
};
