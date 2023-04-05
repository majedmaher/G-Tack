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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->foreignId('customer_id')->constrained('customers', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vendor_id')->constrained('vendors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status' , ['PENDING' , 'ACCEPTED' , 'ON_THE_WAY']);
            $table->text('note')->nullable();
            $table->decimal('total');
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
        Schema::dropIfExists('orders');
    }
};
