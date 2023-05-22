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
            $table->enum('type' , ['ADMIN' , 'USER' , 'CUSTOMER' , 'VENDOR'])->default('CUSTOMER');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('otp');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status' , ['ACTIVE' , 'INACTIVE' , 'WAITING' , 'BLOCK'])->default('ACTIVE');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
