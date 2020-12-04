<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWantWithYouTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('want_with_you', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('who_want_id');
            $table->foreign('who_want_id')->on('users')->references('id');
            $table->unsignedTinyInteger('type');
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
        Schema::dropIfExists('winks');

        Schema::dropIfExists('want_with_you', function (Blueprint $table){
            $table->dropForeign('want_with_you_user_id_foreign');
            $table->dropForeign('want_with_you_who_want_id_foreign');
        });
    }
}
