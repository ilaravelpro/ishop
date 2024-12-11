<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp\Traits;

trait Discount
{
    public function products()
    {
        return $this->belongsToMany(imodal('Product'), 'discounts_products');
    }

    public function terms()
    {
        return $this->belongsToMany(imodal('Term'), 'discounts_terms');
    }

    public function orders()
    {
        return $this->hasMany(imodal('Order'));
    }

    public function carts()
    {
        return $this->hasMany(imodal('Cart'));
    }
}
