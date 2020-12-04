<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPrivateChatRoomMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_private_chat_room_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->string('message');
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->foreign('room_id')->on('users_private_chat_rooms')->references('id')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->dropForeign('users_private_chat_room_messages_room_id_foreign');
            $table->dropForeign('users_private_chat_room_messages_user_id_foreign');
        });
        Schema::dropIfExists('users_private_chat_room_messages');
    }
}
