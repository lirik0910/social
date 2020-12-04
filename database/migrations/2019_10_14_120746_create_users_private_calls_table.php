<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPrivateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_private_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBiginteger('parent_id')->nullable();
            $table->unsignedInteger('caller_user_id');
            $table->unsignedInteger('callee_user_id');
            $table->unsignedInteger('action')->default(1);
            $table->boolean('status')->default(0);
            $table->unsignedBiginteger('meeting_id');
            $table->foreign('parent_id')->references('id')->on('users_private_calls')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('caller_user_id')->on('users')->references('id');
            $table->foreign('callee_user_id')->on('users')->references('id');
            $table->foreign('meeting_id')->on('meetings')->references('id');
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
        Schema::table('users_private_calls', function (Blueprint $table){
            $table->dropForeign('users_private_calls_parent_id_foreign');
            $table->dropForeign('users_private_calls_caller_user_id_foreign');
            $table->dropForeign('users_private_calls_callee_user_id_foreign');
            $table->dropForeign('users_private_calls_meeting_id_foreign');
        });
        Schema::dropIfExists('users_private_call');
    }
}
