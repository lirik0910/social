<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPrivateStreamsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_private_streams_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('tariffing')->default(1);
            $table->boolean('receive_calls')->default(false);
            $table->boolean('photo_verified_only')->default(false);
            $table->boolean('fully_verified_only')->default(false);
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
        Schema::table('user_private_streams_options', function (Blueprint $table) {
            $table->dropForeign('user_private_streams_options_user_id_foreign');
        });

        Schema::dropIfExists('user_private_streams_options');
    }
}
