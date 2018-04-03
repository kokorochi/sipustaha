<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSimpelToPustaha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pustahas', function (Blueprint $table) {
            $table->smallInteger('is_simpel', false, true)->after('author')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pustahas', function (Blueprint $table) {
            $table->dropColumn('is_simpel');
        });
    }
}
