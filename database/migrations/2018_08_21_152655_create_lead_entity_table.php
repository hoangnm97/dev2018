<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_entity', function (Blueprint $table) {
            $table->increments('lead_id');
            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();
            $table->string('name', 100);
            $table->string('phone', 100);
            $table->string('email', 100);

            $table->integer('created_by')->unsigned();
            $table->integer('assigned_to')->unsigned();

            $table->integer('lead_status');
            $table->timestamps();
            $table->softDeletes();

            $table->index('entity_type_id');
            $table->index('created_by');
            $table->index('assigned_to');
            $table->index('lead_status');

            $table->foreign('entity_id')->references('entity_id')->on('entity')->onDelete('cascade');
            $table->foreign('entity_type_id')->references('entity_type_id')->on('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_entity');
    }
}
