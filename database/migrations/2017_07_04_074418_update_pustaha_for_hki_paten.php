<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePustahaForHkiPaten extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pustahas', function (Blueprint $table) {
            $table->string('propose_no')->nullable()->after('pustaha_date');
            $table->string('creator_name')->nullable()->after('propose_no');
            $table->text('creator_address')->nullable()->after('creator_name');
            $table->string('creator_citizenship')->nullable()->after('creator_address');
            $table->string('owner_name')->nullable()->after('creator_citizenship');
            $table->text('owner_address')->nullable()->after('owner_name');
            $table->string('owner_citizenship')->nullable()->after('owner_address');
            $table->string('creation_type')->nullable()->after('owner_citizenship');
            $table->date('announcement_date')->nullable()->after('creation_type');
            $table->string('announcement_place')->nullable()->after('announcement_date');
            $table->string('protection_period')->nullable()->after('announcement_place');
            $table->string('registration_no')->nullable()->after('protection_period');
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
            $table->dropColumn('propose_no');
            $table->dropColumn('creator_name');
            $table->dropColumn('creator_address');
            $table->dropColumn('creator_citizenship');
            $table->dropColumn('owner_name');
            $table->dropColumn('owner_address');
            $table->dropColumn('owner_citizenship');
            $table->dropColumn('creation_type');
            $table->dropColumn('announcement_date');
            $table->dropColumn('announcement_place');
            $table->dropColumn('protection_period');
            $table->dropColumn('registration_no');
        });
    }
}
