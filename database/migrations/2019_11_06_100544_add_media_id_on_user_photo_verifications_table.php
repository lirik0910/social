<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaIdOnUserPhotoVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_photo_verifications', function (Blueprint $table) {
            $table->unsignedInteger('media_id');
            $table->foreign('media_id')->on('media')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_photo_verifications', function (Blueprint $table) {
            $table->dropForeign('user_photo_verifications_media_id_foreign');
            $table->dropColumn('media_id');
        });
    }
}
