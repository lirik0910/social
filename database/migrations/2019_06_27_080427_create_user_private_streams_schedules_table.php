<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPrivateStreamsSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_private_streams_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('private_streams_option_id');
            $table->foreign('private_streams_option_id')->references('id')->on('user_private_streams_options');
            $table->unsignedTinyInteger('weekday');
            $table->time('period_from');
            $table->time('period_to');
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
        Schema::table('user_private_streams_schedules', function (Blueprint $table) {
            $table->dropForeign('user_private_streams_schedules_private_streams_option_id_foreign');
        });

        Schema::dropIfExists('user_private_streams_schedules');
    }
}
