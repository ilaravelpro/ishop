<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iShop\iApp;

class Cart extends Order
{
    public static $s_prefix = 'NMCT';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $bname = 'cart';
    public function items()
    {
        return $this->hasMany(imodal('CartItem'), 'cart_id');
    }

    public function payment_callback($transaction, &$response, $provider)
    {
        if ($response['status']) {
            $order = new (imodal('Order'));
            foreach ($order::getTableColumns() as $tableColumn)
                if (!in_array($tableColumn, ['id', 'created_at', 'updated_at']))
                    $order->$tableColumn =$this->$tableColumn;

            $order->payment_status = 'payed';
            $order->shipping_status = 'processing';
            $order->status = 'processing';
            $order->save();
            $order_item_model = imodal('OrderItem');
            $order_item_columns = $order_item_model::getTableColumns();
            foreach ($this->items as $citem) {
                $item = [];
                foreach ($order_item_columns as $tableColumn)
                    if (!in_array($tableColumn, ['id', 'order_id', 'created_at', 'updated_at']))
                        if ($citem[$tableColumn] !== null) $item[$tableColumn] = $citem[$tableColumn];

                $order->items()->create($item);
            }
            try {
                if (@$order->creator->mobile->text)
                    isms_send("modals.orders.status.payed", @$order->creator->mobile->text, [
                        'number' => @$order->number,
                    ]);
            }catch (\Throwable $exception) {}
            $transaction->update(['model' => 'Order', 'model_id' => $order->id]);
        }
        $response['redirect_method'] = 'GET';
        $response['redirect_uri'] = asset('my' . $this->type);
    }
}
