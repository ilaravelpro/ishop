<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\File;
use iLaravel\Core\iApp\Http\Resources\Resource;

class ShopGateway extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        foreach (ishop('providers.payments.gateways', []) as $item) {
            if (@$data['provider'] && is_string(@$data['provider']) && @$data['provider'] == $item['name']) {
                $data['provider'] = [
                    'text' => $item['title'],
                    'value' => $item['name'],
                    'authenticate' => @$item['authenticate']?:[],
                ];
            }
        }
        return $data;
    }
}
