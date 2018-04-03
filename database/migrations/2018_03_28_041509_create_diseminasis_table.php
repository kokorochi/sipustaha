<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiseminasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diseminasis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pustaha_id', false, true);
            $table->string('file_dissemination_ori')->nullable();
            $table->string('file_dissemination')->nullable();
            $table->string('file_iptek_ori')->nullable();
            $table->string('file_iptek')->nullable();
            $table->string('file_presentation_ori')->nullable();
            $table->string('file_presentation')->nullable();
            $table->string('file_poster_ori')->nullable();
            $table->string('file_poster')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diseminasis');
    }
}
