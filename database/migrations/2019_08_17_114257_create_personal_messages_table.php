<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('recipient_id');
            $table->foreign('recipient_id')->on('users')->references('id');
            $table->text('body');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
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
        Schema::table('personal_messages', function (Blueprint $table){
            $table->dropForeign('personal_messages_user_id_foreign');
            $table->dropForeign('personal_messages_recipient_id_foreign');
        });

        Schema::dropIfExists('personal_messages');
    }
}
