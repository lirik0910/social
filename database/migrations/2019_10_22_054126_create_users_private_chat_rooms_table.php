<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPrivateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_private_chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBiginteger('meeting_id');
            $table->boolean('status')->default(true);
            $table->timestamp('begin_at');
            $table->timestamp('end_at');
            $table->timestamps();

            $table->foreign('meeting_id')->on('meetings')->references('id');
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
            $table->dropForeign('users_private_chat_rooms_meeting_id_foreign');
        });
        Schema::dropIfExists('users_private_chat_rooms');
    }
}
