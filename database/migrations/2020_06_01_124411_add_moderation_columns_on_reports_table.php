<?php

use App\Models\Report;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModerationColumnsOnReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedTinyInteger('moderation_reason')->nullable()->after('reason');
            $table->unsignedTinyInteger('status')->default(Report::STATUS_PENDING)->after('moderation_reason');
            $table->unsignedInteger('reported_user_id')->after('reported_id');
            $table->foreign('reported_user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_reported_user_id_foreign');
            $table->dropColumn('reported_user_id');
            $table->dropColumn('status');
            $table->dropColumn('moderation_reason');
        });
    }
}
