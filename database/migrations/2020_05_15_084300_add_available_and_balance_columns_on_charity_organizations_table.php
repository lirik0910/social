<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableAndBalanceColumnsOnCharityOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charity_organizations', function (Blueprint $table) {
            $table->unsignedInteger('balance')->default(0)->after('description');
            $table->boolean('available')->default(false);
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
            $table->dropColumn('available');
            $table->dropColumn('balance');
        });
    }
}
