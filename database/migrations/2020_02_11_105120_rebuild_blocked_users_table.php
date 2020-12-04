<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RebuildBlockedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('blocked_phone_numbers');
        Schema::dropIfExists('blocked_countries');

        Schema::table('blocked_users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->unsignedInteger('blocked_id')->nullable()->change();
            $table->string('phone_number')->nullable()->after('blocked_id');
            $table->string('phone_title')->nullable()->after('phone_number');
            $table->boolean('blocked_by_phone')->default(false)->after('phone_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocked_users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('phone_title');
            $table->dropColumn('blocked_by_phone');
            $table->softDeletes();
        });
        Schema::create('blocked_phone_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->string('phone_number');
            $table->char('name')->nullable();
            $table->timestamps();
        });
        Schema::create('blocked_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->string('country');
            $table->timestamps();
        });

    }
}
