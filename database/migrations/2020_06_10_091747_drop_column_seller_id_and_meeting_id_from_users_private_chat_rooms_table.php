<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnSellerIdAndMeetingIdFromUsersPrivateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->dropForeign('users_private_chat_rooms_seller_id_foreign');
            $table->dropForeign('users_private_chat_rooms_meeting_id_foreign');
            $table->dropColumn('seller_id');
            $table->dropColumn('meeting_id');
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
            $table->unsignedBiginteger('meeting_id')->nullable();
            $table->foreign('meeting_id')->on('meetings')->references('id');
            $table->unsignedInteger('seller_id')->nullable();
            $table->foreign('seller_id')->on('users')->references('id');
        });
    }
}
