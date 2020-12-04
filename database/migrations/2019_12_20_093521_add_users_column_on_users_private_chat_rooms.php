<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersColumnOnUsersPrivateChatRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('seller_id')->after('user_id');
            $table->foreign('seller_id')->on('users')->references('id');
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
            $table->dropForeign('users_private_chat_rooms_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropForeign('users_private_chat_rooms_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
    }
}
