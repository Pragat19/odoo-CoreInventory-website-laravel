<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_name');
            $table->unsignedBigInteger('product_id');
            $table->decimal('qty', 10, 2);
            $table->enum('status', ['pending', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
}
