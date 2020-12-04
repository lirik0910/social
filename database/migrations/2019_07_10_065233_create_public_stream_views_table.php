<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicStreamViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_stream_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('public_stream_id');
            $table->foreign('public_stream_id')->on('public_streams')->references('id');
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
        Schema::table('public_stream_views', function (Blueprint $table) {
            $table->dropForeign('public_stream_views_user_id_foreign');
            $table->dropForeign('public_stream_views_public_stream_id_foreign');
        });

        Schema::dropIfExists('public_stream_views');
    }
}
