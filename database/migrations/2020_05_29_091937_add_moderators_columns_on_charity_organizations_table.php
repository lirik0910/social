<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeratorsColumnsOnCharityOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charity_organizations', function (Blueprint $table) {
            $table->unsignedTinyInteger('moderation_status')->nullable();
            $table->unsignedTinyInteger('moderation_declined_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charity_organizations', function (Blueprint $table) {
            $table->dropColumn('moderation_status');
            $table->dropColumn('moderation_declined_reason');
        });
    }
}
