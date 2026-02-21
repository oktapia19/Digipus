<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_buku', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Tambah kolom kategori_buku_id di tabel books
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('kategori_buku_id')->nullable()->after('penulis')->constrained('kategori_buku')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['kategori_buku_id']);
            $table->dropColumn('kategori_buku_id');
        });
        Schema::dropIfExists('kategori_buku');
    }
};
