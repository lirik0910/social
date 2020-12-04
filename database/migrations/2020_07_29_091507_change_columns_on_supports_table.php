<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsOnSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->unsignedInteger('status')->default(\App\Models\Support::STATUS_PENDING)->change();
            $table->unsignedInteger('moderator_id')->nullable()->after('user_id');
            $table->foreign('moderator_id')->on('users')->references('id');
            $table->boolean('unviewed_by_user')->default(false);
            $table->boolean('unviewed_by_moderator')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->dropColumn('unviewed_by_user');
            $table->dropColumn('unviewed_by_moderator');
            $table->dropForeign('supports_moderator_id_foreign');
            $table->dropColumn('moderator_id');
            $table->integer('status')->default(0)->change();
        });
    }
}
