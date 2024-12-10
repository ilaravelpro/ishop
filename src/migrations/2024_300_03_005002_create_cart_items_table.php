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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->nullable()->unsigned();
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->bigInteger('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('price_id')->nullable()->unsigned();
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->bigInteger('price_old_id')->nullable()->unsigned();
            $table->foreign('price_old_id')->references('id')->on('price_olds')->onDelete('cascade');
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->nullable()->unsigned();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->bigInteger('weight')->nullable();
            $table->bigInteger('size_x')->nullable();
            $table->bigInteger('size_y')->nullable();
            $table->bigInteger('size_z')->nullable();
            $table->bigInteger('count')->nullable();
            $table->bigInteger('price_first')->nullable();
            $table->bigInteger('price_single')->nullable();
            $table->bigInteger('price_cost')->nullable();
            $table->bigInteger('price_benefit')->nullable();
            $table->bigInteger('price_discount')->nullable();
            $table->bigInteger('price_tax')->nullable();
            $table->bigInteger('price_total')->nullable();
            $table->string('currency')->nullable()->default('IRT');
            $table->integer('position')->default(0);
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
        Schema::dropIfExists('cart_items');
    }
};
