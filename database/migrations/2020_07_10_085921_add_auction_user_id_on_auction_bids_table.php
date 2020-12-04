<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuctionUserIdOnAuctionBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            $table->unsignedInteger('auction_user_id');
            $table->foreign('auction_user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            $table->dropForeign('auction_bids_auction_user_id_foreign');
            $table->dropColumn('auction_user_id');
        });
    }
}
