<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedInteger('admin_id');
            $table->foreign('admin_id')->on('users')->references('id');
            $table->unsignedInteger('amount');
            $table->unsignedTinyInteger('type');
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
        Schema::table('admin_payment_transactions', function (Blueprint $table) {
            $table->dropForeign('admin_payment_transactions_user_id_foreign');
            $table->dropForeign('admin_payment_transactions_admin_id_foreign');
        });

        Schema::dropIfExists('admin_payment_transactions');
    }
}
