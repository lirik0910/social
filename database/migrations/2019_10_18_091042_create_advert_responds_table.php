<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertRespondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advert_responds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('advert_id');
            $table->foreign('advert_id')->on('adverts')->references('id');
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
        Schema::table('advert_responds', function (Blueprint $table) {
            $table->dropForeign('advert_responds_user_id_foreign');
            $table->dropForeign('advert_responds_advert_id_foreign');
        });

        Schema::dropIfExists('advert_responds');
    }
}
