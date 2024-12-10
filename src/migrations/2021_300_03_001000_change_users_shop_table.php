<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 1/18/21, 1:20 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!\Schema::hasColumn($table->getTable(), 'billing_id')) {
                $table->unsignedBigInteger('billing_id')->nullable()->after('avatar_id');
                $table->foreign('billing_id')->references('id')->on('addresses');
            }
            if (!\Schema::hasColumn($table->getTable(), 'shipping_id')) {
                $table->unsignedBigInteger('shipping_id')->nullable()->after('billing_id');
                $table->foreign('shipping_id')->references('id')->on('addresses');
            }
            if (!\Schema::hasColumn($table->getTable(), 'credit')) $table->timestamp('credit')->nullable()->after('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('billing_id');
            $table->dropColumn('shipping_id');
            $table->dropColumn('username');
        });
    }
};
