<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_tables', function (Blueprint $table) {
            $table->string('payment_method')->default('efectivo')->after('client');
        });
    }

    public function down(): void
    {
        Schema::table('sale_tables', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
