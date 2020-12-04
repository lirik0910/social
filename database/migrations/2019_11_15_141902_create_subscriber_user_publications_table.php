<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriberUserPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_user_publications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('subscriber_id');
            $table->unsignedInteger('owner_id');
            $table->morphs('pub');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('subscriber_id')->references('id')->on('users');
            $table->foreign('owner_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriber_user_publications');
    }
}
