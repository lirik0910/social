<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPhotoVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_photo_verifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('verification_photo_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->timestamp('verification_expired_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verification_photo_id')->references('id')->on('photo_verifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_photo_verifications', function (Blueprint $table){
            $table->dropForeign('user_photo_verifications_user_id_foreign');
            $table->dropForeign('user_photo_verifications_verification_photo_id_foreign');
        });
    }
}
