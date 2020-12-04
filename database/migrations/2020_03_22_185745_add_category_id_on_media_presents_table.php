<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdOnMediaPresentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presents', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->after('id');
            $table->foreign('category_id')->on('present_categories')->references('id');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presents', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->dropForeign('presents_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
}
