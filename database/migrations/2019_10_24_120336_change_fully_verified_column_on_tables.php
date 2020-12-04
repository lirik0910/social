<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFullyVerifiedColumnOnTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->boolean('fully_verified_only')->nullable()->change();
        });

        Schema::table('user_meetings_options', function (Blueprint $table) {
            $table->boolean('fully_verified_only')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->boolean('fully_verified_only')->change();
        });

        Schema::table('user_meetings_options', function (Blueprint $table) {
            $table->boolean('fully_verified_only')->change();
        });
    }
}
