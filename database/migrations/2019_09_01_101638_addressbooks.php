<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Addressbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addressbooks', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->string('name',50);
            $table->string('address',50);
            $table->string('faviroute',2);
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
        Schema::dropIfExists('addressbooks');
    }
}
