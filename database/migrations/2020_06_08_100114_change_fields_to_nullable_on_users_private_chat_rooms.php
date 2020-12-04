<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsToNullableOnUsersPrivateChatRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_id')->nullable()->change();
            $table->dateTime('begin_at')->unsigned(true)->change();
            $table->dateTime('end_at')->unsigned(true)->default(null)->change();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_private_chat_rooms', function (Blueprint $table) {
            $table->integer('meeting_id')->nullable(false)->change();
        });
    }
}
