<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('seller_id');
            $table->foreign('seller_id')->on('users')->references('id');
            $table->float('location_lat', 8, 5);
            $table->float('location_lng', 9, 5);
            $table->timestamp('meeting_date');
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('outfit');
            $table->boolean('safe_deal');
            $table->unsignedTinyInteger('status');
            $table->string('confirmation_code')->nullable();
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
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign('meetings_user_id_foreign');
            $table->dropForeign('meetings_seller_id_foreign');
        });

        Schema::dropIfExists('meetings');
    }
}
