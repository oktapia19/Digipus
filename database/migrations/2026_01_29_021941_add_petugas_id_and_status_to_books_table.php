<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('books', function (Blueprint $table) {
        if (!Schema::hasColumn('books', 'petugas_id')) {
            $table->unsignedBigInteger('petugas_id')->nullable();
        }

        if (!Schema::hasColumn('books', 'status')) {
            $table->enum('status', ['pending','approved','rejected','pending_delete'])->default('pending');
        }
    });
}

};
