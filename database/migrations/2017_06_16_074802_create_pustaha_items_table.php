<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePustahaItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pustaha_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pustaha_id', false, true);
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->string('affiliation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pustaha_items');
    }
}
