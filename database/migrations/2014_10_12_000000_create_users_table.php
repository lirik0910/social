<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('role')->default(\App\Models\User::ROLE_USER);
            $table->rememberToken();
            $table->string('phone_verification_code')->nullable();
            $table->timestamp('phone_verification_expired_at')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->unsignedTinyInteger('flags')->default(0);
            $table->string('auth_token', 60)->unique()->nullable();
            $table->timestamp('auth_token_expire_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
