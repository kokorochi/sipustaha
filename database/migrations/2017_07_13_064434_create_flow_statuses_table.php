<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pustaha_id', false, true);
            $table->tinyInteger('item', false, true)->nullable();
            $table->string('status_code', 2)->nullable();
            $table->string('description')->nullable();
            $table->string('created_by', 100)->nullable();
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
        Schema::dropIfExists('flow_statuses');
    }
}
