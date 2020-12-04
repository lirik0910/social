<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->unsignedTinyInteger('type');
            $table->string('preview')->nullable();
            $table->decimal('location_lat', 8, 5);
            $table->decimal('location_lng', 9, 5);
            $table->timestamp('meeting_date');
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('min_age');
            $table->unsignedTinyInteger('max_age');
            $table->unsignedTinyInteger('outfit');
            $table->unsignedInteger('charity_organization_id')->nullable();
            $table->foreign('charity_organization_id')->on('charity_organizations')->references('id');
            $table->boolean('photo_verified_only');
            $table->unsignedInteger('participants')->default(0);
            $table->timestamp('end_at');
            $table->timestamp('ended_at')->nullable();
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
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropForeign('adverts_user_id_foreign');
            $table->dropForeign('adverts_charity_organization_id_foreign');
        });

        Schema::dropIfExists('adverts');
    }
}
