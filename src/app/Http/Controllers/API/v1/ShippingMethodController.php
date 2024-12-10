<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\API\v1;


use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

class ShippingMethodController extends \iLaravel\Core\iApp\Http\Controllers\API\ApiController
{
    public function filters($request, $model, $parent = null, $operators = [])
    {
        $filters = parent::filters($request, $model, $parent, $operators);
        return $filters;
    }

    public function providers(Request $request) {
        return [
            'data' => array_map(function ($item) {
                return [
                    'text' => $item['title'],
                    'value' => $item['name'],
                    'authenticate' => @$item['authenticate']?:[],
                ];
            }, ishop('providers.shipping.methods', []))
        ];
    }
}
