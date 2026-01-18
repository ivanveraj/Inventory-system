<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_sales', function (Blueprint $table) {
            $table->foreignId('day_id')
                ->nullable()
                ->constrained('days')
                ->cascadeOnDelete()
                ->after('id');
        });

        // Best-effort backfill by matching dates.
        DB::statement(
            "UPDATE history_sales hs
             JOIN days d ON DATE(hs.created_at) = DATE(d.created_at)
             SET hs.day_id = d.id
             WHERE hs.day_id IS NULL"
        );
    }

    public function down(): void
    {
        Schema::table('history_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('day_id');
        });
    }
};

