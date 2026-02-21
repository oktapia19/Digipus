<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_relasi', function (Blueprint $table) {
            $table->id();

            // FK ke tabel books
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->cascadeOnDelete();

            // FK ke tabel kategori_buku
            $table->foreignId('kategori_buku_id')
                  ->constrained('kategori_buku')
                  ->cascadeOnDelete();

            // biar ga ada relasi dobel
            $table->unique(['book_id', 'kategori_buku_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_relasi');
    }
};
