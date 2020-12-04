<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->decimal('location_lat', 8, 5);
            $table->decimal('location_lng', 9, 5);
            $table->timestamp('meeting_date');
            $table->unsignedInteger('input_bid');
            $table->unsignedInteger('minimal_step');
            $table->unsignedTinyInteger('min_age');
            $table->unsignedTinyInteger('max_age');
            $table->text('description')->nullable();
            $table->boolean('photo_verified_only');
            $table->boolean('fully_verified_only');
            $table->boolean('location_for_winner_only');
            $table->timestamp('end_at');
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
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropForeign('auctions_user_id_foreign');
        });

        Schema::dropIfExists('auctions');
    }
}
