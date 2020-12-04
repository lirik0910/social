<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnOauthAuthCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('oauth_auth_codes');
            if (!$doctrineTable->hasIndex('oauth_auth_codes_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('oauth_auth_codes_client_id_index')) {
                $table->index('client_id');
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
        Schema::table('oauth_auth_codes', function (Blueprint $table)
        {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['client_id']);
        });
    }
}
