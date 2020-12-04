<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharityOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->char('name');
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->char('payment_receiver_name')->nullable();
            $table->char('payment_receiver_address')->nullable();
            $table->char('payment_receiver_bank')->nullable();
            $table->char('payment_receiver_bank_address')->nullable();
            $table->char('payment_receiver_bank_account')->nullable();
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
        Schema::table('charity_organizations', function (Blueprint $table) {
            $table->dropForeign('charity_organizations_user_id_foreign');
        });

        Schema::dropIfExists('charity_organizations');
    }
}
