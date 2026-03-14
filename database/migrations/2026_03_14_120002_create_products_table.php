<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('stock_qty', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('master_categories')->onDelete('restrict');
            $table->foreign('unit_id')->references('id')->on('master_units')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
