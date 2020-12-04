<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnUserMeetingsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('user_meetings_options', function (Blueprint $table) {
//            $sm = Schema::getConnection()->getDoctrineSchemaManager();
//            $doctrineTable = $sm->listTableDetails('user_meetings_options');
//            if (!$doctrineTable->hasIndex('user_meetings_options_user_id_index')) {
//                $table->index('user_id');
//            }
//            if (!$doctrineTable->hasIndex('user_meetings_options_user_id_index')) {
//                $table->index('user_meetings_options_charity_organization_id');
//            }
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_meetings_options', function (Blueprint $table)
        {

        });
    }
}
