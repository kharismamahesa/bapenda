<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('video')) {
            return;
        }

        Schema::create('video', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('video', 2048);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        // Keep video content intact when rolling back documentation migrations.
    }
};
