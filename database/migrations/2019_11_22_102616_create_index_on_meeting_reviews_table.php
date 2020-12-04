<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnMeetingReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_reviews', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('meeting_reviews');
            if (!$doctrineTable->hasIndex('meeting_reviews_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('meeting_reviews_meeting_id_index')) {
                $table->index('meeting_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_reviews', function (Blueprint $table)
        {

        });
    }
}
