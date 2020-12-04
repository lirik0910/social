<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnUserPrivateStreamsSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_private_streams_schedules', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('user_private_streams_schedules');
            if (!$doctrineTable->hasIndex('user_private_streams_schedules_private_streams_option_id_index')) {
                $table->index('private_streams_option_id');
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
        Schema::table('user_private_streams_schedules', function (Blueprint $table)
        {

        });
    }
}
