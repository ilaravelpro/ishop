<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 4/3/20, 7:49 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->bigInteger('cart_id')->nullable()->unsigned();
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->bigInteger('gateway_id')->nullable()->unsigned();
            $table->foreign('gateway_id')->references('id')->on('shop_gateways')->onDelete('cascade');
            $table->string('number')->nullable();
            $table->string('provider')->nullable();
            $table->string('ip')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('currency')->nullable()->default('IRT');
            $table->string('reference_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('card_name')->nullable();
            $table->string('card_number')->nullable();
            $table->text('card_hash')->nullable();
            $table->longText('last_code')->nullable();
            $table->longText('last_message')->nullable();
            $table->longText('send_request')->nullable();
            $table->longText('send_response')->nullable();
            $table->longText('verify_request')->nullable();
            $table->longText('verify_response')->nullable();
            $table->longText('hash')->nullable();
            $table->string('status')->default('processing');
            $table->timestamp('payed_at')->nullable();
            $table->timestamp('checked_at')->nullable();
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
        Schema::dropIfExists('order_payments');
    }
};
