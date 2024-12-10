<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp;

class ShippingConfig extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMSMC';
    public static $s_start = 24300000;
    public static $s_end = 728999999;

    public $with_resource = ['cities' => 'CityData'];

    protected $casts = [
        'config' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(imodal('User'));
    }
    public function method()
    {
        return $this->belongsTo(imodal('ShippingMethod'), 'method_id');
    }
    public function cities()
    {
        return $this->belongsToMany(imodal('City'), 'shipping_configs_cities', 'city_id', 'config_id');
    }
    public function additionalUpdate($request = null, $additional = null, $parent = null)
    {
        try {
            $this->cities()->detach();
            $cityModal = imodal('City');
            $this->cities()->attach(array_map(function ($v) use($cityModal) {return $cityModal::id($v)?:$v; }, $request->cities?:[]));
        }catch (\Throwable $exception) {}
        parent::additionalUpdate($request, $additional, $parent);
    }
    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    'method_id' => "nullable|exists:shipping_methods,id",
                    'cities.*' => "nullable|exists:cities,id",
                    'price_km' => "nullable|numeric",
                    'price_min' => "nullable|numeric",
                    'price_max' => "nullable|numeric",
                    'weight_min' => "nullable|numeric",
                    'weight_max' => "nullable|numeric",
                    'price_currency' => "nullable|string|in:IRT",
                    'config' => "nullable",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.shipping_configs', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
