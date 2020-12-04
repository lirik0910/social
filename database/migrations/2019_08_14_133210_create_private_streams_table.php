<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_streams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('seller_id');
            $table->foreign('seller_id')->on('users')->references('id');
            $table->unsignedInteger('tariffing');
            $table->unsignedTinyInteger('status')->default(\App\Models\PrivateStream::STATUS_NEW);
            $table->unsignedInteger('presents_cost')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('private_streams');

        Schema::dropIfExists('private_streams', function (Blueprint $table){
            $table->dropForeign('private_streams_user_id_foreign');
            $table->dropForeign('private_streams_seller_id_foreign');
        });
    }
}
