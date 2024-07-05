<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->integer('type')->default(1);
            $table->string('email')->unique();
            $table->tinyInteger('gender')->comment('1:male, 2:female, 3:other');
            $table->date('birthday')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(1)->comment('1:active, 2:inactive');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('code');
            $table->dateTime('expired_time')->nullable();
            $table->tinyInteger('completed')->default(0)->comment('0:use , 1:NotUse');
            $table->dateTime('completed_at')->nullable();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('activations');
    }
};
