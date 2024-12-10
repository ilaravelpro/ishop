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
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->bigInteger('method_id')->nullable()->unsigned();
            $table->foreign('method_id')->references('id')->on('shipping_methods')->onDelete('cascade');
            $table->bigInteger('address_id')->nullable()->unsigned();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->string('number')->nullable();
            $table->string('provider')->nullable();
            $table->text('tracking_id')->nullable();
            $table->text('tracking_url')->nullable();
            $table->string('receiver')->nullable();
            $table->text('receiver_description')->nullable();
            $table->text('operator_description')->nullable();
            $table->text('address_description')->nullable();
            $table->text('returned_description')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->bigInteger('tax')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->bigInteger('insurance')->nullable();
            $table->bigInteger('total')->nullable();
            $table->string('currency')->nullable()->default('IRT');
            $table->bigInteger('weight')->nullable();
            $table->longText('last_code')->nullable();
            $table->longText('last_message')->nullable();
            $table->longText('send_request')->nullable();
            $table->longText('send_response')->nullable();
            $table->longText('verify_request')->nullable();
            $table->longText('verify_response')->nullable();
            $table->longText('hash')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('warehouse_out_at')->nullable();
            $table->timestamp('origin_out_at')->nullable();
            $table->timestamp('destination_in_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('returned_at')->nullable();
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
