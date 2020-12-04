<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnUsersPrivateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_private_calls', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('users_private_calls');
            if (!$doctrineTable->hasIndex('users_private_calls_parent_id_index')) {
                $table->index('parent_id');
            }
            if (!$doctrineTable->hasIndex('users_private_calls_caller_user_id_index')) {
                $table->index('caller_user_id');
            }
            if (!$doctrineTable->hasIndex('users_private_calls_callee_user_id_index')) {
                $table->index('callee_user_id');
            }
            if (!$doctrineTable->hasIndex('users_private_calls_meeting_id_index')) {
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
        Schema::table('users_private_calls', function (Blueprint $table)
        {

        });
    }
}
