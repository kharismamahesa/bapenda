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
        if (!Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->increments('id_berita');
                $table->string('id_berita_kategori', 10)->default('1');
                $table->string('judul', 250);
                $table->text('isi');
                $table->string('thumbnail', 250)->nullable();
                $table->string('gambar', 250)->nullable();
                $table->dateTime('tanggal_berita')->nullable();
                $table->integer('counter')->default(0);
                $table->string('url', 250)->nullable();
                $table->integer('status')->default(1);
                $table->dateTime('created_datetime')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};

