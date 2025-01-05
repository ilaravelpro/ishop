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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('billing_id')->nullable()->unsigned();
            $table->foreign('billing_id')->references('id')->on('locations')->onDelete('cascade');
            $table->bigInteger('discount_id')->nullable()->unsigned();
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->bigInteger('payment_gateway_id')->nullable()->unsigned();
            $table->foreign('payment_gateway_id')->references('id')->on('shop_gateways')->onDelete('cascade');
            $table->string('number')->nullable();
            $table->bigInteger('copies')->default(0);
            $table->bigInteger('products_total')->default(0);
            $table->bigInteger('discount_price')->default(0);
            $table->bigInteger('discount_code')->default(0);
            $table->bigInteger('discount_order')->default(0);
            $table->bigInteger('discount_tax')->default(0);
            $table->bigInteger('discount_total')->default(0);
            $table->bigInteger('invoice_total')->default(0);
            $table->bigInteger('tax_total')->default(0);
            $table->bigInteger('payment_total')->default(0);
            $table->string('currency')->nullable()->default('IRT');
            $table->string('local')->nullable();
            $table->string('status')->default('draft');
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
        Schema::dropIfExists('carts');
    }
};
