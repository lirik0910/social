<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUsersPrivateChatRoomMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users_private_chat_room_messages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('users_private_chat_room_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->string('message');
            $table->unsignedInteger('price');
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->foreign('room_id')->on('users_private_chat_rooms')->references('id')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id');

            $table->softDeletes();
        });

    }
}
