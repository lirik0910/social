<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnSubscriberUserPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriber_user_publications', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('subscriber_user_publications');
            if (!$doctrineTable->hasIndex('subscriber_user_publications_subscriber_id_index')) {
                $table->index('subscriber_id');
            }
            if (!$doctrineTable->hasIndex('subscriber_user_publications_owner_id_index')) {
                $table->index('owner_id');
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
        Schema::table('subscriber_user_publications', function (Blueprint $table)
        {

        });
    }
}
