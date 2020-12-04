<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateStreamMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_stream_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('private_stream_id');
            $table->foreign('private_stream_id')->on('private_streams')->references('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('recipient_id');
            $table->foreign('recipient_id')->on('users')->references('id');
            $table->string('body');
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
        Schema::table('private_stream_messages', function (Blueprint $table){
            $table->dropForeign('private_stream_messages_user_id_foreign');
            $table->dropForeign('private_stream_messages_private_stream_id_foreign');
            $table->dropForeign('private_stream_messages_recipient_id_foreign');
        });

        Schema::dropIfExists('private_stream_messages');
    }
}
