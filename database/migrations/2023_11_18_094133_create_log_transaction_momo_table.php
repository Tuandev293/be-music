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
        Schema::create('log_transaction_momo', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('user_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('trans_id')->nullable();
            $table->string('message')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('status')->comment('0: chưa thanh toán, 1: đã thanh toán, 2: hoàn tiền');
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
        Schema::dropIfExists('log_transaction_momo');
    }
};
