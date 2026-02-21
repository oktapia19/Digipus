<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategori_relasi', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('book_id');
    $table->unsignedBigInteger('kategori_id');

    $table->foreign('book_id')
          ->references('id')
          ->on('books')
          ->onDelete('cascade');

    $table->foreign('kategori_id')
          ->references('id')
          ->on('kategori_buku')
          ->onDelete('cascade');

    $table->unique(['book_id', 'kategori_id']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_relasi');
    }
};
