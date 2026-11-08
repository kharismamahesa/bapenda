<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pengaduan')) {
            Schema::create('pengaduan', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('kode', 20)->nullable();
                $table->string('nama', 256);
                $table->string('email', 256);
                $table->string('no_hp', 15)->nullable();
                $table->text('aduan');
                $table->integer('id_uptup')->nullable();
                $table->dateTime('created_datetime')->nullable();
                $table->dateTime('updated_datetime');
                $table->integer('updated_by')->nullable();
                $table->integer('status')
                    ->comment('0=menunggu 1=aktif 2=publish 3=ditangguhkan 4=publis');
                $table->primary('id');
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');
            });
        }

        if (! Schema::hasTable('pengaduan_balas')) {
            Schema::create('pengaduan_balas', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('id_pengaduan', 20)
                    ->nullable()
                    ->charset('utf8mb4')
                    ->collation('utf8mb4_0900_ai_ci');
                $table->text('balasan');
                $table->dateTime('updated_datetime');
                $table->integer('updated_by')->nullable();
                $table->integer('status');
                $table->primary('id');
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');
            });
        }
    }

    public function down(): void
    {
        // These tables may already contain manually maintained complaint data.
    }
};
