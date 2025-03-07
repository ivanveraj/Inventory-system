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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('category');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->string('keywords')->nullable();
            $table->double('buyprice')->default(0);
            $table->double('saleprice')->default(0);
            $table->double('utility')->default(0);
            $table->double('amount')->default(0);
            $table->double('discount')->default(0);
            $table->date('discount_to')->nullable();
            $table->double('iva')->nullable();
            $table->boolean('is_activated')->default(true);
            $table->boolean('has_stock_alert')->default(false);
            $table->unsignedInteger('min_stock_alert')->nullable();
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
        Schema::dropIfExists('products');
    }
};
