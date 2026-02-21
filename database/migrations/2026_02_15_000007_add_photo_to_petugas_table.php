php artisan migrate
php artisan storage:link<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('no_telepon');
        });
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
