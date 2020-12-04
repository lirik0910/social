<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscribersCountColumnOnPublicStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_streams', function (Blueprint $table) {
            $table->unsignedInteger('subscribers_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_streams', function (Blueprint $table) {
            $table->dropColumn('subscribers_count');
        });
    }
}
