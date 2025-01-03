<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/08/29 Sun 04:42 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

Route::namespace('v1')->prefix('v1')->middleware('auth:api')->group(function () {
    Route::apiResource('shop', 'ShopController', ['as' => 'api']);
    Route::get('orders/statics', 'OrderController@statics')->name('api.orders.statics');
    Route::apiResource('orders', 'OrderController', ['as' => 'api']);
    Route::post('orders/{record}/cancel', 'OrderController@cancel')->name('api.orders.cancel');
    Route::post('orders/{record}/send_sms', 'OrderController@send_sms')->name('api.orders.send_sms');
    Route::apiResource('orders/{record}/items', 'OrderItemController', ['as' => 'api']);

    Route::apiResource('carts', 'CartController', ['as' => 'api']);
    Route::post('carts/{record}/append/{product}', 'CartController@append')->name('api.carts.append');
    Route::post('carts/{record}/decrease/{index}', 'CartController@decrease')->name('api.carts.decrease');
    Route::post('carts/{record}/increase/{index}', 'CartController@increase')->name('api.carts.increase');
    Route::post('carts/{record}/remove/{index}', 'CartController@remove')->name('api.carts.remove');
    Route::post('carts/{record}/discount/use', 'CartController@discountUse')->name('api.carts.discount_use');
    Route::post('carts/{record}/discount/remove', 'CartController@discountRemove')->name('api.carts.discount_remove');
    Route::post('carts/{record}/gateway', 'CartController@gateway')->name('api.carts.gateway');
    Route::post('carts/{record}/shipping', 'CartController@shipping')->name('api.carts.shipping');
    Route::post('carts/{record}/address', 'CartController@address')->name('api.carts.address');
    Route::post('carts/{record}/pay', 'CartController@toPay')->name('api.carts.pay');
});
