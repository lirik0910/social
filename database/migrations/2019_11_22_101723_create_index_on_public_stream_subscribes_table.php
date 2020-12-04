<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnPublicStreamSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_stream_subscribes', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('public_stream_subscribes');
            if (!$doctrineTable->hasIndex('public_stream_subscribes_public_stream_id_index')) {
                $table->index('public_stream_id');
            }
            if (!$doctrineTable->hasIndex('public_stream_subscribes_user_id_index')) {
                $table->index('user_id');
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
        Schema::table('public_stream_subscribes', function (Blueprint $table)
        {

        });
    }
}
