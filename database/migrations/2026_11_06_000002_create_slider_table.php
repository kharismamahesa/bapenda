<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('slider')) {
            return;
        }

        Schema::create('slider', function (Blueprint $table) {
            $table->id();
            $table->string('gambar');
            $table->string('judul_putih', 150);
            $table->string('judul_kuning', 150);
            $table->text('subjudul');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Keep slider content intact when rolling back documentation migrations.
    }
};
