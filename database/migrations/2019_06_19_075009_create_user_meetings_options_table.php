<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMeetingsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_meetings_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('minimal_price')->default(0);
            $table->unsignedTinyInteger('min_age')->default(25);
            $table->unsignedTinyInteger('max_age')->default(80);
            $table->boolean('safe_deal_only')->default(false);
            $table->boolean('photo_verified_only')->default(true);
            $table->boolean('fully_verified_only')->default(true);
            $table->unsignedInteger('charity_organization_id')->nullable();
            $table->foreign('charity_organization_id')->references('id')->on('charity_organizations');
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
        Schema::table('user_meetings_options', function (Blueprint $table) {
            $table->dropForeign('user_meetings_options_user_id_foreign');
            $table->dropForeign('user_meetings_options_charity_organization_id_foreign');
        });


        Schema::dropIfExists('user_meetings_options');
    }
}
