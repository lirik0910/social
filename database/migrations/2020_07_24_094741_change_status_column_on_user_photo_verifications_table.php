<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusColumnOnUserPhotoVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_photo_verifications', function (Blueprint $table) {
            $table->unsignedInteger('status')->default(\App\Models\UserPhotoVerification::STATUS_NEW)->change();
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
            $table->unsignedInteger('status')->default(\App\Models\UserPhotoVerification::STATUS_PENDING)->change();
        });
    }
}
