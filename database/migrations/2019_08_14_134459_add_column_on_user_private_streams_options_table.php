<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOnUserPrivateStreamsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_private_streams_options', function (Blueprint $table) {
            $table->unsignedTinyInteger('min_age')->default(25);
            $table->unsignedTinyInteger('max_age')->default(80);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_private_streams_options', function (Blueprint $table) {
            $table->dropColumn('min_age');
            $table->dropColumn('max_age');
        });
    }
}
