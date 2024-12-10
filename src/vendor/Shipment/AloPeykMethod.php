<?php

namespace iLaravel\iShop\Vendor\Shipment;

class AloPeykMethod extends StaticMethod
{

    public $model;
    public $configs;
    public $static_configs;
    public $percent_configs;
    public $value_configs;

    public $order = null;

    public function __construct($model)
    {
        parent::__construct($model);
        \AloPeyk\AloPeykApiHandler::setToken(@$model->authenticate['private_key']);
       // \AloPeyk\AloPeykApiHandler::setEndpoint('sandbox');
    }

    public function amount($order, $address, $weight = null)
    {
        if (!$address)
            return false;
        if ($order->warehouse->address->city_id != @$address->city_id)
            return false;
        if (!(@$order->warehouse->address->latitude && @$order->warehouse->address->longitude && @$address->latitude && @$address->longitude))
            return 0;
        $price = parent::amount($order, $address, $weight);
        $origin = new \AloPeyk\Model\Address('origin', round($order->warehouse->address->latitude, 6), round($order->warehouse->address->longitude,6));
        $dest = new \AloPeyk\Model\Address('destination', round($address->latitude, 6), round($address->longitude,6));
        $order = new \AloPeyk\Model\Order('motorbike', $origin, $dest);
        $order->setHasReturn(false);
        $apiResponse = $order->getPrice();
        $price += @$apiResponse->object->price?:0;
        return $price > 0 ? (ceil($price / 100) * 100) : 0;
    }

    public function request($order, $address)
    {
        $order = is_numeric($order) ? \AloPeyk\Model\Order::findOrFail($order) : $order;
        if (!($order->warehouse->latitude && $order->warehouse->longitude && $address->latitude && $address->longitude))
            return false;
        if ($this->model->status == 'active'){
            $origin = new \AloPeyk\Model\Address('origin', round($order->warehouse->latitude, 6), round($order->warehouse->longitude,6));
            $origin->setDescription($order->warehouse->address->text);
            $origin->setPersonFullname($order->warehouse->address->title);
            $origin->setPersonPhone($order->warehouse->address->phone);
            $dest = new \AloPeyk\Model\Address('destination', round($address->latitude, 6), round($address->longitude,6));
            $dest->setDescription($order->address->text);
            $dest->setPersonFullname($order->title);
            $dest->setPersonPhone($order->creator?->mobile?->text);
            $orderAloPeyk = new \AloPeyk\Model\Order('motorbike', $origin, $dest);
            $orderAloPeyk->setHasReturn(false);
            $orderAloPeyk->setCashed(false);
            $apiResponse = $orderAloPeyk->create();
            $id = '';
            $price = 100000;
            $tracking_url = $this->model->tracking_url?:'https://tracking.alopeyk.com/#/%id%';
            if ($apiResponse && $apiResponse->status == "success"){
                $id = $apiResponse->object->order_token;
                $price = $apiResponse->object->price;
            }
            $shipment = $order->shipments()->updateOrCreate(['method_id' => $this->model->id, 'address_id' => $address->id],
                [
                    'user_id' => $order->creator_id,
                    'amount' => $price,
                    'tax' => 0,
                    'discount' => 0,
                    'insurance' => 0,
                    'total' => $price,
                    'weight' => $order->weight_total,
                    'tracking_url' => $tracking_url,
                    'tracking_id' => $id,
                    'status' => 'processing',
                ]);
            return $price;
        }
        return false;
    }
}
