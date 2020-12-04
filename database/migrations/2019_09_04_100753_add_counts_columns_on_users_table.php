<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountsColumnsOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('subscribers_count')->default(0);
            $table->unsignedSmallInteger('subscribes_count')->default(0);
            $table->unsignedSmallInteger('blocked_count')->default(0);
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
            $table->dropColumn('subscribers_count');
            $table->dropColumn('subscribes_count');
            $table->dropColumn('blocked_count');
        });
    }
}
