<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\WEB;


use iLaravel\iShop\Vendor\PaymentService;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

class OrderPaymentController extends \iLaravel\Core\iApp\Http\Controllers\WEB\Controller
{
    public $endpoint = \iLaravel\iShop\iApp\Http\Controllers\API\v1\OrderPaymentController::class;

    public function callback(Request $request, $payment, $method, $order) {
        $orderModel = imodal('Order');
        $order = $orderModel::findBySerial($order);
        $paymentModel = imodal('OrderPayment');
        $payment = $paymentModel::findBySerial($payment);
        $methodModel = imodal('ShopGateway');
        $method = $methodModel::findBySerial($method);
        if ($payment->payed_at) {
            abort("404","This payment has already been registered.");
        }
        $result = PaymentService::verify($payment);
        if ($result['status']){
            try {
                $order = @$payment->order()->first();
                if (@$order->shipping_method) @$order->shipping_method?->service?->request(@$order, @$order->shipping);
            }catch (\Throwable $exception){}
            return redirect(route('account.orders') . '/' . $result['order']);
            //return redirect_post(route('account.orders'), $result, true);
        }
        else
            return redirect(route('shop.checkout') . "?payment=" . $payment->serial);
    }
    public function redirects(Request $request, $payment, $method, $order) {
        $orderModel = imodal('Order');
        $order = $orderModel::findBySerial($order);
        $paymentModel = imodal('OrderPayment');
        $payment = $paymentModel::findBySerial($payment);
        $methodModel = imodal('ShopGateway');
        $method = $methodModel::findBySerial($method);
        if ($payment->payed_at) {
            abort("404","This payment has already been registered.");
        }
        return PaymentService::redirect($payment);
    }
}
