<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        'survei' => [
            'survei_created_date_spam_index' => ['created_date', 'spam_flagged_at'],
            'survei_office_created_spam_index' => ['id_uptup', 'created_date', 'spam_flagged_at'],
        ],
        'pengaduan' => [
            'pengaduan_created_datetime_index' => ['created_datetime'],
            'pengaduan_office_created_index' => ['id_uptup', 'created_datetime'],
        ],
        'pengaduan_balas' => [
            'pengaduan_balas_id_pengaduan_index' => ['id_pengaduan'],
        ],
        'visitor_logs' => [
            'visitor_logs_visited_date_index' => ['visited_date'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $tableName => $indexes) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($indexes as $indexName => $columns) {
                if (! Schema::hasIndex($tableName, $indexName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                        $table->index($columns, $indexName);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $tableName => $indexes) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach (array_keys($indexes) as $indexName) {
                if (Schema::hasIndex($tableName, $indexName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                }
            }
        }
    }
};
