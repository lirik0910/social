<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign('profiles_user_id_foreign');
        });

        Schema::dropIfExists('profiles');

        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('image')->nullable();
            $table->char('nickname')->unique()->nullable();
            $table->date('age')->nullable();
            $table->unsignedTinyInteger('sex')->nullable();
            $table->unsignedTinyInteger('dating_preference')->nullable();
            $table->char('country')->nullable();
            $table->char('region')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat')->nullable();
            $table->decimal('lng')->nullable();
            $table->char('name')->nullable();
            $table->char('surname')->nullable();
            $table->char('timezone')->default('UTC');
            $table->unsignedTinyInteger('height')->nullable();
            $table->unsignedTinyInteger('physique')->nullable();
            $table->unsignedTinyInteger('appearance')->nullable();
            $table->unsignedTinyInteger('eye_color')->nullable();
            $table->unsignedTinyInteger('hair_color')->nullable();
            $table->char('occupation')->nullable();
            $table->unsignedTinyInteger('marital_status')->nullable();
            $table->boolean('kids')->nullable();
            $table->json('languages')->nullable();
            $table->unsignedTinyInteger('smoking')->nullable();
            $table->unsignedTinyInteger('alcohol')->nullable();
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
}
