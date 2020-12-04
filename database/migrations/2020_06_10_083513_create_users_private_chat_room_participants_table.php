<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPrivateChatRoomParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_private_chat_room_participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->foreign('room_id')->on('users_private_chat_rooms')->references('id')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_private_chat_room_participants', function (Blueprint $table) {
            $table->dropForeign('users_private_chat_room_participants_room_id_foreign');
            $table->dropForeign('users_private_chat_room_participants_user_id_foreign');
        });
        Schema::dropIfExists('users_private_chat_room_participants');
    }
}
