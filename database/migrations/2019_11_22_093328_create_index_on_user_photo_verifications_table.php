<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOnUserPhotoVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_photo_verifications', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('user_photo_verifications');
            if (!$doctrineTable->hasIndex('user_photo_verifications_user_id_index')) {
                $table->index('user_id');
            }
            if (!$doctrineTable->hasIndex('user_photo_verifications_verification_photo_id_index')) {
                $table->index('verification_photo_id');
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
        Schema::table('user_photo_verifications', function (Blueprint $table)
        {
        });
    }
}
