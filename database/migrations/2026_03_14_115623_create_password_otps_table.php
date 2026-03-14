<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordOtpsTable extends Migration
{
    public function up()
    {
        Schema::create('password_otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_otps');
    }
}
