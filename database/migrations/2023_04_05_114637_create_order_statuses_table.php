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
        Schema::create('order_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('customer_id')->constrained('customers', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vendor_id')->constrained('vendors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('reason_id')->nullable()->constrained('reasons', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status' , ['PENDING' , 'DELIVERING' , 'ACCEPTED' , 'RECEIVED' , 'DECLINED' , 'ONWAY' , 'PROCESSING' , 'FILLED' , 'DELIVERED' , 'COMPLETED' , 'CANCELLED_BY_VENDOR' , 'CANCELLED_BY_CUSTOMER']);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('order_statuses');
    }
};
