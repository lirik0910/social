<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnPrivateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_streams', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('private_streams');
            if (!$doctrineTable->hasIndex('private_streams_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('private_streams_seller_id_index')) {
                $table->index('seller_id');
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
        Schema::table('private_streams', function (Blueprint $table)
        {

        });
    }
}
