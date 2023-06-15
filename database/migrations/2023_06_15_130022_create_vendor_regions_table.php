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
        Schema::create('vendor_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('region_id')->constrained('locations', 'id')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('vendor_regions');
    }
};
