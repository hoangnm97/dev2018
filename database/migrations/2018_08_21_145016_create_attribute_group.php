<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_group', function (Blueprint $table) {
            $table->increments('attribute_group_id');
            $table->integer('entity_type_id')->unsigned();
            $table->string('attribute_group_name', 100);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('attribute_group');
    }
}
