<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertUserIdOnAdvertRespondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advert_responds', function (Blueprint $table) {
            $table->unsignedInteger('advert_user_id');
            $table->foreign('advert_user_id')->on('users')->references('id');
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
            $table->dropForeign('advert_responds_advert_user_id_foreign');
            $table->dropColumn('advert_user_id');
        });
    }
}
