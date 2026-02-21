<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('tanggal_masuk_akun')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_masuk_akun',
                'nama_lengkap',
                'alamat',
                'no_telepon'
            ]);
        });
    }
};
