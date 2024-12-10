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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('image_id')->nullable()->unsigned();
            $table->foreign('image_id')->references('id')->on('posts')->onDelete('cascade');
            $table->bigInteger('country_id')->nullable()->unsigned();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('provider')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('price_first')->nullable();
            $table->bigInteger('price_km')->nullable();
            $table->bigInteger('price_min')->nullable();
            $table->bigInteger('price_max')->nullable();
            $table->bigInteger('weight_min')->nullable();
            $table->bigInteger('weight_max')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('price_currency')->nullable()->default('IRT');
            $table->integer('order')->default(0);
            $table->string('local')->nullable();
            $table->string('status')->default('draft');
            $table->longText('authenticate')->nullable();
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
        Schema::dropIfExists('shipping_methods');
    }
};
