<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('visitor_logs')) {
            return;
        }

        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('browser', 100);
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('visited_date');
            $table->timestamps();

            $table->index(['ip_address', 'visited_date']);
            $table->unique(
                ['ip_address', 'browser', 'visited_date'],
                'visitor_logs_daily_visitor_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
