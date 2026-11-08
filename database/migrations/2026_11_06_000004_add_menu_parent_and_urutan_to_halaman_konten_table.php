<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halaman_konten', function (Blueprint $table) {
            $table->enum('menu_parent', ['profil', 'layanan'])->nullable()->after('gambar');
            $table->unsignedInteger('urutan')->default(0)->after('menu_parent');
        });

    }

    public function down(): void
    {
        Schema::table('halaman_konten', function (Blueprint $table) {
            $table->dropColumn(['menu_parent', 'urutan']);
        });
    }
};
