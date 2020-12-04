<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaPresentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_presents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('media_id')->unsigned();
            $table->integer('present_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->float('price')->default(0);
            $table->timestamps();
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->foreign('present_id')->references('id')->on('presents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_presents', function (Blueprint $table){
            $table->dropForeign('media_presents_media_id_foreign');
            $table->dropForeign('media_presents_present_id_foreign');
            $table->dropForeign('media_presents_user_id_foreign');
        });

        Schema::dropIfExists('media_presents');
    }
}
