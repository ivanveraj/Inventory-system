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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user')->unique();
            $table->smallInteger('state');
            $table->unsignedBigInteger('rol_id');
            $table->foreign('rol_id')->references('id')->on('rols');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
