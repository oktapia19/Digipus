<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                // Nama petugas
            $table->string('email')->unique();     // Email login
            $table->string('password');            // Password (harus di-hash)
            $table->string('alamat')->nullable();  // Alamat petugas
            $table->string('no_telepon')->nullable(); // Nomor telepon
            $table->timestamps();                  // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
