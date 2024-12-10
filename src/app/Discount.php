<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp;

class Discount extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMDC';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $find_names = ['slug', 'code'];

    public $files = ['image'];

    protected $casts = [
        'groups' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(imodal('User'));
    }

    public function user()
    {
        return $this->belongsTo(imodal('User'));
    }

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

    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [
            'terms.*' => "nullable",
            'products.*' => "nullable",
        ];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    'title' => "required|string",
                    'code' => ['nullable','string'],
                    'use' => 'nullable|numeric',
                    'used' => 'nullable|numeric',
                    'type' => "nullable|string",
                    'value' => 'nullable|numeric',
                    'price_min' => 'nullable|numeric',
                    'quantity_min' => 'nullable|numeric',
                    'start_at' => "nullable|date_format:Y-m-d H:i:s",
                    'end_at' => "nullable|date_format:Y-m-d H:i:s",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.discounts', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
