<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePustahaMoreUploadFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pustahas', function (Blueprint $table) {
            $table->string('file_claim_request_ori')->after('file_name')->nullable();
            $table->string('file_claim_request')->after('file_claim_request_ori')->nullable();
            $table->string('file_claim_accomodation_ori')->after('file_claim_request')->nullable();
            $table->string('file_claim_accomodation')->after('file_claim_accomodation_ori')->nullable();
            $table->string('file_certification_ori')->after('file_claim_accomodation')->nullable();
            $table->string('file_certification')->after('file_certification_ori')->nullable();
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
            $table->dropColumn('file_claim_request_ori');
            $table->dropColumn('file_claim_request');
            $table->dropColumn('file_claim_accomodation_ori');
            $table->dropColumn('file_claim_accomodation');
            $table->dropColumn('file_certification_ori');
            $table->dropColumn('file_certification');
        });
    }
}
