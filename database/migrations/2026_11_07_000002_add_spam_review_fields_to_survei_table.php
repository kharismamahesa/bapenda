<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('survei')) {
            throw new RuntimeException('Tabel survei harus tersedia sebelum menambahkan penanda spam.');
        }

        Schema::table('survei', function (Blueprint $table) {
            if (! Schema::hasColumn('survei', 'spam_flagged_at')) {
                $table->dateTime('spam_flagged_at')->nullable()->after('created_date');
                $table->index('spam_flagged_at');
            }

            if (! Schema::hasColumn('survei', 'spam_reason')) {
                $table->string('spam_reason', 100)->nullable()->after('spam_flagged_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('survei')) {
            return;
        }

        Schema::table('survei', function (Blueprint $table) {
            if (Schema::hasColumn('survei', 'spam_reason')) {
                $table->dropColumn('spam_reason');
            }

            if (Schema::hasColumn('survei', 'spam_flagged_at')) {
                $table->dropIndex(['spam_flagged_at']);
                $table->dropColumn('spam_flagged_at');
            }
        });
    }
};
