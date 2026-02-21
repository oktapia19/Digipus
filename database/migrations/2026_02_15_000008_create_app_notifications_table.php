<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_type', 20); // admin|petugas|user
            $table->unsignedBigInteger('recipient_id');
            $table->string('title', 255);
            $table->text('message')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['recipient_type', 'recipient_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};

