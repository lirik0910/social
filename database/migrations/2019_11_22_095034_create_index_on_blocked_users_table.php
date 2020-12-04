<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnBlockedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocked_users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('blocked_users');
            if (!$doctrineTable->hasIndex('blocked_users_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('blocked_users_blocked_id_index')) {
                $table->index('blocked_id');
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
        Schema::table('blocked_users', function (Blueprint $table)
        {

        });
    }
}
