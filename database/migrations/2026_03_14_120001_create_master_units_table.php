<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMasterUnitsTable extends Migration
{
    public function up()
    {
        Schema::create('master_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        DB::table('master_units')->insert([
            ['name' => 'pcs',  'display_name' => 'Pieces',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kg',   'display_name' => 'Kilogram',    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'g',    'display_name' => 'Gram',        'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ltr',  'display_name' => 'Litre',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ml',   'display_name' => 'Millilitre',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mtr',  'display_name' => 'Meter',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cm',   'display_name' => 'Centimeter',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'box',  'display_name' => 'Box',         'created_at' => now(), 'updated_at' => now()],
            ['name' => 'doz',  'display_name' => 'Dozen',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pair', 'display_name' => 'Pair',        'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('master_units');
    }
}
