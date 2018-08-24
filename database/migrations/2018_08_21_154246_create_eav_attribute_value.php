<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEavAttributeValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eav_attribute_value', function (Blueprint $table) {
            $table->increments('attribute_value_id');
            $table->integer('attribute_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->string('value', 255)->comment('value of attribute');
            $table->timestamps();

            $table->foreign('attribute_id')->references('attribute_id')->on('eav_attribute')->onDelete('cascade');
            $table->foreign('entity_id')->references('entity_id')->on('entity')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eav_attribute_value');
    }
}
