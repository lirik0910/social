<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('meetings');
            if (!$doctrineTable->hasIndex('meetings_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('meetings_seller_id_index')) {
                $table->index('seller_id');
            }
            if (!$doctrineTable->hasIndex('meetings_charity_organization_id_index')) {
                $table->index('charity_organization_id');
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
        Schema::table('meetings', function (Blueprint $table)
        {

        });
    }
}
