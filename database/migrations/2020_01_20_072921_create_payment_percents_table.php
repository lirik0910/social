<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPercentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_percents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('model');
            $table->unsignedInteger('percent');
            $table->unsignedInteger('type');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_percents');
    }
}
