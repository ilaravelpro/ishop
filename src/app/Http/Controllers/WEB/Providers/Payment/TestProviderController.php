<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 2/4/21, 11:36 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\WEB\Providers\Payment;

use iLaravel\Core\iApp\Http\Controllers\WEB\Controller;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;


class TestProviderController extends Controller
{
    public $endpoint = null;
    public function __construct(Request $request)
    {
        if (!$request->route()) return;
        parent::__construct($request);
        self::$result = new \StdClass;
        $this->designConstruct($request);
        if (!$this->endpoint){
            $endpoint = iapicontroller(class_basename($this));
            if (class_exists($endpoint)) $this->endpoint = $endpoint;
        }
    }
    public function show(Request $request, $payment) {
        $model = imodal('OrderPayment');
        $payment = $model::findBySerial($payment);
        if ($payment->payed_at) {
            abort("403", "This payment has already been registered.");
        }
        return view('plugins.iorder.payments.test', ['payment' => $payment]);
    }

    public function back(Request $request, $payment) {
        $model = imodal('OrderPayment');
        $payment = $model::findBySerial($payment);
        if ($payment->payed_at) {
            abort("403", "This payment has already been registered.");
        }
        return redirect_post($payment->send_request['callback'], $request->all());
    }
}
