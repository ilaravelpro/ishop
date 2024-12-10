<?php

namespace iLaravel\iShop\Vendor;


class ShipmentService
{
    public static $providers = [
        'static' => Shipment\StaticMethod::class,
        'alopeyk' => Shipment\AloPeykMethod::class,
    ];
    public static function provider($name, $model) :Shipment\StaticMethod | bool {
        return @static::$providers[$name] ? (new static::$providers[$name]($model)) : false;
    }
}
