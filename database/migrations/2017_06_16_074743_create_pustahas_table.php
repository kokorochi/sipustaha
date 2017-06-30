<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePustahasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pustahas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pustaha_type', false, true);
            $table->string('author', 100)->nullable();
            $table->text('title')->nullable();
            $table->text('name')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('publisher')->nullable();
            $table->string('editor')->nullable();
            $table->string('isbn_issn')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue')->nullable();
            $table->string('url_address')->nullable();
            $table->smallInteger('halaman', false, true)->nullable();
            $table->tinyInteger('year', false, true)->nullable();
            $table->tinyInteger('month', false, true)->nullable();
            $table->tinyInteger('day', false, true)->nullable();
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
        Schema::dropIfExists('pustahas');
    }
}
