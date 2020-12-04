<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveBeginAndColumnOnUsersPrivateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->dropColumn('begin_at');
            $table->dropColumn('end_at');
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
            $table->dateTime('begin_at')->unsigned(true)->change();
            $table->dateTime('end_at')->unsigned(true)->default(null)->change();
        });
    }
}
