<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnOnFeedViewedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feed_viewed', function (Blueprint $table) {
            $table->renameColumn('public_streams_ids', 'adverts_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feed_viewed', function (Blueprint $table) {
            $table->renameColumn('adverts_ids', 'public_streams_ids');
        });
    }
}
