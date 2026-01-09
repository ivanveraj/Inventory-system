<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->datetime('opened_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->double('opening_balance')->default(0);
            $table->double('cash_sales')->default(0);
            $table->double('card_sales')->default(0);
            $table->double('transfer_sales')->default(0);
            $table->double('total_sales')->default(0);
            $table->double('total')->default(0);
            $table->double('profit')->default(0);
            $table->double('tables_total')->default(0);
            $table->double('products_total')->default(0);
            $table->double('expenses')->default(0);
            $table->double('withdrawals')->default(0);
            $table->double('cash_left_for_next_day')->default(0);
            $table->double('final_balance')->default(0);
            $table->foreignId('opened_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('finish_day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('days');
    }
};
