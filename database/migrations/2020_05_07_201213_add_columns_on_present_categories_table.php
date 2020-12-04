<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnPresentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('present_categories', function (Blueprint $table) {
            $table->string('size');
            $table->string('mimetype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('present_categories', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('mimetype');
        });
    }
}
