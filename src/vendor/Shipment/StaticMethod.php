<?php

namespace iLaravel\iShop\Vendor\Shipment;


class StaticMethod
{

    public $model;
    public $configs;
    public $static_configs;
    public $percent_configs;
    public $value_configs;

    public $order = null;
    public $user = null;


    public function __construct($model)
    {
        $this->model = $model;
        $this->configs = $model->configs()->with('cities')->get();
        $this->percent_configs = $model->configs->where('type', 'percent');
        $this->static_configs = $model->configs->where('type', 'value')->whereIn('weight_min', [null, ''])->whereIn('weight_max', [null, ''])->sortByDesc('type')->values();
        $this->value_configs = $model->configs->where('type', 'value')->whereNotIn('weight_min', [null, ''])->whereNotIn('weight_max', [null, ''])->sortByDesc('type')->values();
    }

    public static function fast($model) {
        return (new static($model));
    }

    public function amount($order, $address, $weight = null)
    {
        $city = $address->city;
        $price = 0;
        $weight = $weight?:$order->weight_total;
        foreach ($this->static_configs as $index => $config) {
            if ($config->cities->count() == 0 || ($config->cities->count() > 0 && $config->cities->where('id', $city->id)->first())) {
                if ($index > 0 && $config->type == 'percent') {
                    $price += ($price * $config->price_first) / 100;
                }else $price += $config->price_first;
            }
        }
        foreach ($this->value_configs->where('weight_max', '>', 0)->values() as $config) {
            if ($config->cities->count() == 0 || ($config->cities->count() > 0 && $config->cities->where('id', $city->id)->first())) {
                if ($config->type == 'percent') {
                    $price += ($price * $config->price_first) / 100;
                }else $price += $config->price_first;
            }
        }
        if (($max_config = $this->value_configs->whereIn('weight_max', [0, null, ''])->first()) &&
            ($max_config->cities->count() == 0 || ($max_config->cities->count() > 0 && $max_config->cities->where('id', $city->id)->first()))) {
            if ($max_config->type == 'percent') {
                $price += ($price * $max_config->price_first) / 100;
            }else $price += round(($weight - $max_config->weight_min) / $max_config->weight_min, 2) * $max_config->price_first;
        }
        foreach ($this->percent_configs as $index => $config) {
            if ($config->cities->count() == 0 ||
                ($config->cities->count() > 0 && $config->cities->where('id', $city->id)->first()))
                $price += ($price * $config->price_first) / 100;
        }

        return $price > 0 ? (ceil($price / 100) * 100) : 0;
    }
    public function request($order, $address)
    {
        $order = is_numeric($order) ? \AloPeyk\Model\Order::findOrFail($order) : $order;
        if (!($order->warehouse->address->postcode && $address->postcode))
            return false;
        if ($this->model->status == 'active'){
            $price = $this->amount($order, $address);
            $shipment = $order->shipments()->updateOrCreate(['method_id' => $this->model->id, 'address_id' => $address->id],
                [
                    'user_id' => $order->creator_id,
                    'amount' => $price,
                    'tax' => 0,
                    'discount' => 0,
                    'insurance' => 0,
                    'total' => $price,
                    'weight' => $order->weight_total,
                    'tracking_url' => $this->model->tracking_url,
                    'status' => 'processing',
                ]);
            return $price;
        }
        return false;
    }
    public function getUrl($id)
    {
        return str_replace('%id%', $id?:'%id%', $this->model->tracking_url);
    }
}
