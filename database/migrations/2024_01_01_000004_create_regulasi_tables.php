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
        if (! Schema::hasTable('regulasi_kategori')) {
            Schema::create('regulasi_kategori', function (Blueprint $table) {
                $table->integer('id_regulasi_kategori', true)->primary();
                $table->string('regulasi_kategori', 200)
                    ->nullable()
                    ->charset('latin1')
                    ->collation('latin1_swedish_ci');
            });
        }

        if (! Schema::hasTable('regulasi')) {
            Schema::create('regulasi', function (Blueprint $table) {
                $table->integer('id_regulasi', true)->primary();
                $table->text('regulasi')
                    ->nullable()
                    ->charset('latin1')
                    ->collation('latin1_swedish_ci');
                $table->integer('id_regulasi_kategori')->nullable();
                $table->text('keterangan')->nullable();
                $table->text('file')->nullable();
                $table->dateTime('created_datetime')->nullable();
                $table->integer('created_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These tables may predate this migration and contain production data.
    }
};
