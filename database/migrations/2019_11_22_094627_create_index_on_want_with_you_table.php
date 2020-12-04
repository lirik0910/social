<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnWantWithYouTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('want_with_you', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('want_with_you');
            if (!$doctrineTable->hasIndex('want_with_you_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('want_with_you_who_want_id_index')) {
                $table->index('who_want_id');
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
        Schema::table('want_with_you', function (Blueprint $table)
        {

        });
    }
}
