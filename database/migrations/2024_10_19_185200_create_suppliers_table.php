<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del proveedor
            $table->string('category'); //Categoria
            $table->string('contact_person')->nullable(); // Persona de contacto
            $table->string('email')->unique()->nullable(); // Correo electrónico
            $table->string('phone')->nullable(); // Teléfono
            $table->string('address')->nullable(); // Dirección
            $table->json('schedule')->nullable(); // Campo para días en formato JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
