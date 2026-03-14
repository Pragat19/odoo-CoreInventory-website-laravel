<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('counted', 10, 2);
            $table->decimal('difference', 10, 2)->default(0);
            $table->enum('status', ['draft', 'validated', 'cancelled'])->default('draft');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('warehouses')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_adjustments');
    }
}
