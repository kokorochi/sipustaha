<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePustahaAddApproveByAndAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pustahas', function (Blueprint $table) {
            $table->string('approved_by_1')->after('file_name')->nullable();
            $table->string('approved_by_2')->after('approved_by_1')->nullable();
            $table->integer('amount_id')->after('registration_no')->nullable();
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
            $table->dropColumn('approved_by');
            $table->dropColumn('amount_id');
        });
    }
}
