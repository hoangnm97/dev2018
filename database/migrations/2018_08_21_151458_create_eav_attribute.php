<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEavAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eav_attribute', function (Blueprint $table) {
            $table->increments('attribute_id');
            $table->integer('attribute_group_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();
            $table->string('attribute_name', 100);
            $table->string('data_type', 50)->comment('String, integer, datetime...');
            $table->string('frontend_tye', 50)->comment('text, number, picklist...');
            $table->timestamps();

            $table->foreign('attribute_group_id')->references('attribute_group_id')->on('attribute_group')->onDelete('cascade');
            $table->foreign('entity_type_id')->references('entity_type_id')->on('entity_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eav_attribute');
    }
}
