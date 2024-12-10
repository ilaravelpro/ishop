<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 1/24/21, 9:08 AM
 * Copyright (c) 2021. Powered by iamir.net
 */
Route::any('callbacks/payment/{payment}/{method}/{order}', 'OrderPaymentController@callback')->name('callbacks.payment');
Route::any('redirects/payment/{payment}/{method}/{order}', 'OrderPaymentController@redirects')->name('redirects.payment');
Route::namespace('Providers\Payment')->prefix('providers/payment')->group(function () {
    Route::get('test/{payment}', 'TestProviderController@show')->name('payment.providers.test.show');
    Route::post('test/{payment}', 'TestProviderController@back')->name('payment.providers.test.back');
});
