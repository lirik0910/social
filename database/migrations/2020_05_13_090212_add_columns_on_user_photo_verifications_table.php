<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnUserPhotoVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_photo_verifications', function (Blueprint $table) {
            $table->string('decline_reason')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Models\UserPhotoVerification::STATUS_PENDING);
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
            $table->dropColumn('status');
            $table->dropColumn('decline_reason');
        });
    }
}
