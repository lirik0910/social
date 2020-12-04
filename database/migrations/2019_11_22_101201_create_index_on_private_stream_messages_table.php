<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnPrivateStreamMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_stream_messages', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('private_stream_messages');
            if (!$doctrineTable->hasIndex('private_stream_messages_private_stream_id_index')) {
                $table->index('private_stream_id');
            }
            if (!$doctrineTable->hasIndex('private_stream_messages_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('private_stream_messages_recipient_id_index')) {
                $table->index('recipient_id');
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
        Schema::table('private_stream_messages', function (Blueprint $table)
        {

        });
    }
}
