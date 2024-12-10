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
        Schema::create('shipping_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('method_id')->nullable()->unsigned();
            $table->foreign('method_id')->references('id')->on('shipping_methods')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('type')->nullable()->default('value');
            $table->bigInteger('price_first')->nullable();
            $table->bigInteger('price_km')->nullable();
            $table->bigInteger('price_min')->nullable();
            $table->bigInteger('price_max')->nullable();
            $table->bigInteger('weight_min')->nullable();
            $table->bigInteger('weight_max')->nullable();
            $table->string('price_currency')->nullable()->default('IRT');
            $table->boolean('is_all_cities')->default(0);
            $table->integer('order')->default(0);
            $table->string('local')->nullable();
            $table->string('status')->default('active');
            $table->longText('meta')->nullable();
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
        Schema::dropIfExists('shipping_configs');
    }
};
