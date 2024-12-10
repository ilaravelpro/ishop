<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\File;
use iLaravel\Core\iApp\Http\Resources\Resource;

class Order extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['payment_status_text'] = _t($this->payment_status);
        $data['shipping_status_text'] = _t($this->shipping_status);
        $data['status_text'] = _t($this->status);
        return $data;
    }
}
