<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPaymentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->integer('amount')->unsigned()->default(0)->change();
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedInteger('external_id')->nullable();
            $table->unsignedInteger('pay_amount')->nullable();
            $table->char('currency', 3)->nullable();
            $table->unsignedTinyInteger('external_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->float('amount')->default(0.00)->change();
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'pay_amount', 'currency', 'external_status']);
        });
    }
}
