<?php

namespace iLaravel\iShop\Vendor;


use Carbon\Carbon;

class PaymentService
{
    public static $providers = [
        'test' => Payment\TestMethod::class,
        'saman' => Payment\SamanMethod::class,
        'parsian' => Payment\ParsianMethod::class,
    ];
    public static function provider($name, $model) :Payment\TestMethod | bool {
        return @static::$providers[$name] ? (new static::$providers[$name]($model)) : false;
    }

    public static function model() {
        return imodal('OrderPayment');
    }

    public static function send($order)
    {
        $amount = $order->payment_total;
        $mobile = $order->creator->mobile;
        $email = $order->creator->email;
        $gateway = $order->payment_gateway;
        $provider = static::provider($gateway->provider, $gateway);
        if (!$provider) return ['status' => false, 'message' => 'Payment method not found.', 'code' => 404];
        $payment = static::model()::create([
            $order::$bname . "_id" => $order->id,
            "gateway_id" => $order->payment_gateway_id,
            "provider" => $order->payment_gateway->provider,
            "ip" => _get_user_ip(),
            "amount" => $amount,
            "currency" => $order->currency,
        ]);
        $request = [
            'amount' => $amount,
            'callback' => route('callbacks.payment', [
                'payment' => $payment->serial,
                'method' => $order->payment_gateway->serial,
                'order' => $order->serial,
            ]),
            'description' => $order->description,
            'currency' => $order->currency,
            'mobile' => $mobile ? $mobile->text : null,
            'email' => $email ? $email->text : null
        ];
        $result = $provider->request($order, $payment, ...$request);
        $payment->send_request = $request;
        $payment->send_response = $result;
        $payment->last_code = $result['code'];
        $payment->last_message = $result['message'];
        if ($result['status']){
            $payment->status = 'paying';
            $payment->transaction_id = $result['transaction_id'];
        }else {
            $payment->status = 'bank_error';
        }
        $payment->save();
        $out = ['status' => $result['status'], 'message' => $result['message'], 'code' => $result['code']];
        if ($result['status']){
            $out['endpoint'] = route('redirects.payment', [
                'order' => $order->serial,
                'method' => $order->payment_gateway->serial,
                'payment' => $payment->serial,
            ]);
            $out['transaction_id'] = $result['transaction_id'];
        }
        return $out;
    }
    public static function redirect($payment)
    {
        $provider = static::provider($payment->gateway->provider, $payment->gateway);
        if (!$provider) abort(404);
        return $provider->redirect($payment);
    }
    public static function verify($payment)
    {
        $provider = static::provider($payment->gateway->provider, $payment->gateway);
        if (!$provider) return ['status' => false, 'message' => 'Payment not found.', 'code' => 404];
        $result = $provider->verify($payment->order, $payment);
        $payment->verify_request = [
            'amount' => $payment->amount,
            'currency' => ($payment->order?:$payment->cart)->currency
        ];
        $payment->verify_response = $result;
        $payment->last_code = $result['code'];
        $payment->last_message = $result['message'];
        $response = ['status' => $result['status'], 'message' => $result['message'], 'code' => $result['code']];
        if ($result['status']){
            $payment->status = 'payed';
            $payment->reference_id = @$result['reference_id'];
            $payment->payment_id = @$result['payment_id'];
            $payment->payed_at = Carbon::now()->format('Y-m-d H:i:s');
            if (!$payment->order) {
                $order = new (imodal('Order'));
                foreach ($order::getTableColumns() as $tableColumn) {
                    if (!in_array($tableColumn, ['id', 'created_at', 'updated_at'])) {
                        $order->$tableColumn = $payment->cart->$tableColumn;
                    }
                }
                $order->payment_status = 'payed';
                $order->shipping_status = 'processing';
                $order->status = 'processing';
                $order->save();
                $order_item_model = imodal('OrderItem');
                $order_item_columns = $order_item_model::getTableColumns();
                foreach ($payment->cart->items as $index => $citem) {
                    $item = [];
                    foreach ($order_item_columns as $tableColumn) {
                        if (!in_array($tableColumn, ['id', 'order_id', 'created_at', 'updated_at'])) {
                            if ($citem[$tableColumn] !== null) $item[$tableColumn] = $citem[$tableColumn];
                        }
                    }
                   $order->items()->create($item);
                }
                try {
                    if (@$order->creator->mobile->text)
                        isms_send("modals.orders.status.payed", @$order->creator->mobile->text, [
                            'number' => @$order->number,
                        ]);
                }catch (\Throwable $exception) {}
                $payment->order_id = $order->id;
                $response['order'] = $order->number;
            }
        }else
            $payment->status = $result['status'];
        $payment->save();
        if ($result['status']) {
            if ($payment->order) {
                $payment->order->status = 'processing';
                $payment->order->payment_status = 'payed';
                $payment->order->shipping_status = 'processing';
                $payment->order->save();
                $response['order'] = $payment->order->number;
            }
            if ($payment->cart) {
                $payment->cart->status = 'ordered';
                $payment->cart->save();
            }
        }
        return $response;
    }
}
