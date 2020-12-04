<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_to_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('permission_id');
            $table->foreign('permission_id')->references('id')->on('admin_permissions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('admin_to_permissions', function (Blueprint $table) {
//            $table->dropForeign('admin_to_permissions_user_id_foreign');
//            $table->dropForeign('admin_to_permissions_permission_id_foreign');
//        });

        Schema::dropIfExists('admin_to_permissions');
    }
}
