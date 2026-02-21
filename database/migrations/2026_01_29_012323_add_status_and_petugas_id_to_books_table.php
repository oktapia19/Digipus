<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('status', ['pending','approved'])->default('approved');
            $table->unsignedBigInteger('petugas_id')->nullable();

            $table->foreign('petugas_id')
                  ->references('id')
                  ->on('petugas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['petugas_id']);
            $table->dropColumn(['status','petugas_id']);
        });
    }
};
