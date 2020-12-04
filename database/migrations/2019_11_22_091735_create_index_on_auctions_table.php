<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('auctions');
            if (!$doctrineTable->hasIndex('auctions_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('auctions_participants_index')) {
                $table->index('participants');
            }
            if (!$doctrineTable->hasIndex('auctions_charity_organization_id_index')) {
                $table->index('charity_organization_id');
            }
            if (!$doctrineTable->hasIndex('auctions_last_bid_id_index')) {
                $table->index('last_bid_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auctions', function (Blueprint $table)
        {

        });
    }
}
