<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnMediaPresentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_presents', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('media_presents');
            if (!$doctrineTable->hasIndex('media_presents_media_id_index')) {
                $table->index('media_id');
            }
            if (!$doctrineTable->hasIndex('media_presents_present_id_index')) {
                $table->index('present_id');
            }
            if (!$doctrineTable->hasIndex('media_presents_user_id_index')) {
                $table->index('user_id');
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
        Schema::table('media_presents', function (Blueprint $table)
        {

        });
    }
}
