<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/02/05 Fri 06:39 AM IRST
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\Providers;

use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends \Illuminate\Foundation\Support\Providers\AuthServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        Gate::resource('orders', 'iLaravel\Core\Vendor\iRole\iRolePolicy');
        Gate::resource('orders.items', 'iLaravel\Core\Vendor\iRole\iRolePolicy');
        Gate::resource('carts', 'iLaravel\Core\Vendor\iRole\iRolePolicy');
        Gate::define('carts.append', 'iLaravel\Core\Vendor\iRole\iRolePolicy@show');
        Gate::resource('shop', 'iLaravel\Core\Vendor\iRole\iRolePolicy');
    }
}
