<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp;

class ShippingMethod extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMSM';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $find_names = ['name'];

    public $files = ['image'];

    public $with_resource = ['cities' => 'CityData'];
    public $with_resource_single = ['configs'];

    protected $casts = ['authenticate' => 'array'];


    public function creator()
    {
        return $this->belongsTo(imodal('User'));
    }
    public function configs()
    {
        return $this->hasMany(imodal('ShippingConfig'), 'method_id');
    }
    public function cities()
    {
        return $this->belongsToMany(imodal('City'), 'shipping_configs_cities', 'city_id', 'config_id');
    }
    public function getServiceAttribute()
    {
        return \iLaravel\iShop\Vendor\ShipmentService::provider($this->provider, $this);
    }
    public function additionalUpdate($request = null, $additional = null, $parent = null)
    {
        _save_child($this->configs(), $request->configs?:[], imodal('ShippingConfig'), ['method_id' => $this->id], ['cities']);
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
        $additionalRules = [
            'image_file' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            'cities.*' => "nullable|exists:cities,id",
            'configs.*.cities.*' => "nullable|exists:cities,id",
            'configs.*.title' => "nullable|string",
            'configs.*.type' => "nullable|string|in:percent,value",
            'configs.*.price_km' => "nullable|numeric",
            'configs.*.price_min' => "nullable|numeric",
            'configs.*.price_max' => "nullable|numeric",
            'configs.*.weight_min' => "nullable|numeric",
            'configs.*.weight_max' => "nullable|numeric",
            'configs.*.price_currency' => "nullable|string|in:IRT",
        ];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    'title' => "required|string",
                    'name' => ['nullable','string'],
                    'provider' => 'nullable|string|in:static,alopeyk',
                    'description' => "nullable|string",
                    'price_km' => "nullable|numeric",
                    'price_min' => "nullable|numeric",
                    'price_max' => "nullable|numeric",
                    'weight_min' => "nullable|numeric",
                    'weight_max' => "nullable|numeric",
                    'authenticate.*' => "nullable|string",
                    'price_currency' => "nullable|string|in:IRT",
                    'is_all_cities' => "nullable|boolean",
                    'website' => "nullable|string",
                    'tracking_url' => "nullable|string",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.shipping_methods', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
