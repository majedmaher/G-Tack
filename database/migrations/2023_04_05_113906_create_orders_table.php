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
            $table->enum('type' , ['GAS' , 'WATER']);
            $table->integer('number');
            $table->foreignId('customer_id')->constrained('customers', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('governorate_id')->nullable()->constrained('locations', 'id')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('region_id')->nullable()->constrained('locations', 'id')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('vendor_id')->constrained('vendors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status' , ['PENDING' , 'ACCEPTED' , 'DECLINED' , 'DELIVERING' , 'RECEIVED' , 'ONWAY' , 'PROCESSING' , 'FILLED' , 'DELIVERED' , 'COMPLETED' , 'CANCELLED_BY_VENDOR' , 'CANCELLED_BY_CUSTOMER']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('time')->nullable();
            $table->text('note')->nullable();
            $table->decimal('total');
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
        Schema::dropIfExists('orders');
    }
};
