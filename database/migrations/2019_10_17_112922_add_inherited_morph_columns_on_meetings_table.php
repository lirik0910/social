<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInheritedMorphColumnsOnMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->nullableMorphs('inherited');

            $table->dropForeign('meetings_auction_id_foreign');
            $table->dropColumn('auction_id');
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
            $table->dropMorphs('inherited');

            $table->unsignedInteger('auction_id')->nullable();
            $table->foreign('auction_id')->on('auctions')->references('id');
        });
    }
}
