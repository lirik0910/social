<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnPersonalMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_messages', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('personal_messages');
            if (!$doctrineTable->hasIndex('personal_messages_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('personal_messages_recipient_id_index')) {
                $table->index('recipient_id');
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
        Schema::table('personal_messages', function (Blueprint $table)
        {

        });
    }
}
