<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesBackgroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles_backgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedTinyInteger('type')->default(\App\Models\ProfilesBackground::TYPE_IMAGE);
            $table->string('name');
            $table->string('mimetype');
            $table->string('size');
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
        Schema::dropIfExists('profiles_backgrounds');

        Schema::dropIfExists('media_presents', function (Blueprint $table){
            $table->dropForeign('private_streams_user_id_foreign');
        });
    }
}
