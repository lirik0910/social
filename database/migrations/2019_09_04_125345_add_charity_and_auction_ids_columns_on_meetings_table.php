<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharityAndAuctionIdsColumnsOnMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->unsignedInteger('charity_organization_id')->nullable();
            $table->foreign('charity_organization_id')->on('charity_organizations')->references('id');
            $table->unsignedInteger('auction_id')->nullable();
            $table->foreign('auction_id')->on('auctions')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign('meetings_charity_organization_id_foreign');
            $table->dropForeign('meetings_auction_id_foreign');
            $table->dropColumn('charity_organization_id');
            $table->dropColumn('auction_id');
        });
    }
}
