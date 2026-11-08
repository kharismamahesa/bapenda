<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin', 'uptup') NOT NULL DEFAULT 'admin'");
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'uptup')->exists()) {
            throw new RuntimeException('Tidak dapat menghapus role uptup selama masih digunakan oleh akun.');
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin') NOT NULL DEFAULT 'admin'");
    }
};
