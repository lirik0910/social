<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('type')->nullable();
            $table->string('name');
            $table->string('mimetype');
            $table->string('size');
            $table->string('description')->nullable();
            $table->integer('views')->default(0);
            $table->float('presents')->default(0);
            $table->integer('status')->unsigned()->nullable();
            $table->integer('reason')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('media', function (Blueprint $table){
            $table->dropForeign('media_user_id_foreign');
        });
    }
}
