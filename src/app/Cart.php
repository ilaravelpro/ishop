<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp;

class Cart extends Order
{
    public static $s_prefix = 'NMCT';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $bname = 'cart';
    public function items()
    {
        return $this->hasMany(imodal('CartItem'), 'cart_id');
    }
}
