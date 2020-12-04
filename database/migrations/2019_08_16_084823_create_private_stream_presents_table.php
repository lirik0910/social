<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateStreamPresentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_stream_presents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('private_stream_id');
            $table->foreign('private_stream_id')->on('private_streams')->references('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('present_id');
            $table->foreign('present_id')->on('presents')->references('id');
            $table->unsignedInteger('price');
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
        Schema::table('private_stream_presents', function (Blueprint $table){
            $table->dropForeign('private_stream_presents_user_id_foreign');
            $table->dropForeign('private_stream_presents_private_stream_id_foreign');
            $table->dropForeign('private_stream_presents_present_id_foreign');
        });

        Schema::dropIfExists('private_stream_presents');
    }
}
