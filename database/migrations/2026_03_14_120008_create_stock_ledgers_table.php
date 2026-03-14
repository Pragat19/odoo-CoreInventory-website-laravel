<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockLedgersTable extends Migration
{
    public function up()
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('product_id');
            $table->enum('operation', ['receipt', 'transfer', 'delivery', 'adjustment']);
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->decimal('qty', 10, 2);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_ledgers');
    }
}
