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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type' , ['ALL' , 'GAS' , 'WATER']);
            $table->string('name');
            $table->string('slug')->nullable();
            $table->boolean('is_required');
            $table->enum('file' , ['IMAGE' , 'FILE']);
            $table->enum('status' , ['ACTIVE' , 'INACTIVE']);
            $table->unsignedInteger('validity')->default(0);
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
        Schema::dropIfExists('documents');
    }
};
