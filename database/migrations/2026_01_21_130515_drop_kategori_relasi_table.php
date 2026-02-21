<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::dropIfExists('kategori_relasi');
}

public function down()
{
    Schema::create('kategori_relasi', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('book_id');
        $table->unsignedBigInteger('kategori_id');
    });
}

};
