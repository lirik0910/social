<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePhotoVerifiedOnlyColumnOnUserMeetingsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_meetings_options', function (Blueprint $table) {
            $table->boolean('photo_verified_only')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_meetings_options', function (Blueprint $table) {
            $table->boolean('photo_verified_only')->default(true)->change();
        });
    }
}
