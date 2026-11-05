<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wilayah_samsat')) {
            return;
        }

        Schema::create('wilayah_samsat', function (Blueprint $table) {
            $table->unsignedSmallInteger('KodeWilayah')->primary();
            $table->string('NamaWilayah', 50)->nullable();
            $table->smallInteger('KodeKabupatenKota')->default(0);
            $table->string('Status', 10)->default('');
            $table->string('NamaUPTUP', 255)->default('');
            $table->index(
                ['KodeKabupatenKota', 'Status', 'NamaUPTUP'],
                'wilayah_samsat_kodekab_status_namauptup_index'
            );
        });
    }

    public function down(): void
    {
        // The table may already contain manually maintained wilayah data.
    }
};
