<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/02/05 Fri 06:39 AM IRST
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\Providers;

use Illuminate\Support\Facades\View;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole())
        {
            $this->loadMigrationsFrom(ishop_path('migrations'));
        }

        $this->mergeConfigFrom(ishop_path('config/shop.php'), 'ilaravel.main.ishop');

        View::addLocation(ishop_path('resources/views'));
        $this->app->singleton('current_cart', function(){
            return auth()->user()->current_cart?: auth()->user()->carts()->create(['status' => 'filling', 'warehouse_id' => @imodal('Warehouse')::where('is_default', 1)->first()->id?:1]);
        });
    }

    public function register()
    {
        parent::register();
    }
}
