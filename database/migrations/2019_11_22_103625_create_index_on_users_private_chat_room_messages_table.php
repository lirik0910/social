<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnUsersPrivateChatRoomMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_chat_room_messages', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('users_private_chat_room_messages');
            if (!$doctrineTable->hasIndex('users_private_chat_room_messages_room_id_index')) {
                $table->index('room_id');
            }
            if (!$doctrineTable->hasIndex('users_private_chat_room_messages_user_id_index')) {
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
        Schema::table('users_private_chat_room_messages', function (Blueprint $table) {

        });
    }
}
