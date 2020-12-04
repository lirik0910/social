<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockedColumnOnUsersPrivateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false);
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
            $table->dropColumn('is_blocked');
        });

    }
}
