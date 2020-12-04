<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('image')->nullable();
            $table->char('nickname')->unique()->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->char('sex')->nullable();
            $table->char('dating_preference')->nullable();
            $table->char('country')->nullable();
            $table->char('city')->nullable();
            $table->char('name')->nullable();
            $table->char('surname')->nullable();
            $table->char('timezone')->default('UTC');
            $table->unsignedTinyInteger('height')->nullable();
            $table->char('physique')->nullable();
            $table->char('appearance')->nullable();
            $table->char('eye_color')->nullable();
            $table->char('hair_color')->nullable();
            $table->char('occupation')->nullable();
            $table->char('marital_status')->nullable();
            $table->boolean('kids')->nullable();
            $table->char('languages')->nullable();
            $table->char('smoking')->nullable();
            $table->text('about')->nullable();
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
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign('profiles_user_id_foreign');
        });

        Schema::dropIfExists('profiles');
    }
}
