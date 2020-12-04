<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnSubscriberIdFromSubscriberUserPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriber_user_publications', function (Blueprint $table) {
            $table->dropForeign('subscriber_user_publications_subscriber_id_foreign');
            $table->dropIndex('subscriber_user_publications_subscriber_id_index');
            $table->dropColumn('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriber_user_publications', function (Blueprint $table) {
            $table->unsignedInteger('subscriber_id');
            $table->foreign('subscriber_id')->references('id')->on('users');
            $table->index('subscriber_id');
        });
    }
}
