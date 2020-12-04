<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveColumnsFromProfilesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('nickname');
            $table->dropColumn('timezone');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('password');
            $table->char('nickname')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('nickname');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->char('nickname')->unique()->nullable();
            $table->char('timezone')->default('UTC');
        });
    }
}
