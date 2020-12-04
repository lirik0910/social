<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsOnAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->unsignedTinyInteger('outfit');
            $table->unsignedInteger('charity_organization_id')->nullable();
            $table->foreign('charity_organization_id')->on('charity_organizations')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropForeign('auctions_charity_organization_id_foreign');
            $table->dropColumn('charity_organization_id');
            $table->dropColumn('outfit');
        });
    }
}
