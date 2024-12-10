<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\API\v1;


use iLaravel\Core\iApp\Http\Controllers\API\ApiChildController;

class OrderItemController extends ApiChildController
{
    public $parentController = OrderController::class;
    public $statusFilter = false;
}
