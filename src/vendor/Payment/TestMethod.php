<?php

namespace iLaravel\iShop\Vendor\Payment;


class TestMethod
{

    public $model;
    public $configs;

    public $order = null;

    public function __construct($model)
    {
        $this->model = $model;
    }
    public static function fast($model) {
        return (new static($model));
    }
    public function request($order, $log, $amount, $callback, $description, $currency, $mobile = null, $email = null) {
        $input = [
            'amount' => $amount,
            'callback' => $callback,
            'description' => $description,
            'currency' => $currency,
            'mobile' => $mobile,
            'email' => $email,
        ];
        $output = [
            'token' => md5($order->serial.$log->serial.$amount.$currency),
        ];
        return [
            'status' => true,
            'endpoint' => route('payment.providers.test.show', ['payment' => $log->serial]),
            'transaction_id' => $output['token'],
            'message' => _t("Token created successfully."),
            'code' => 0,
            'input' => $input,
            'output' => $output,
        ];
    }
    public function redirect($payment) {
        return redirect(route('payment.providers.test.show', ['payment' => $payment->serial]));
    }
    public function verify($order, $log) {
        $request = request()->all();
        $status = _get_value($request, 'status', 2) == 1;
        return [
            'status' => $status,
            'state' => $status ? 'successful' : 'unsuccessful',
            'transaction_id' => request('transaction_id'),
            'message' => $status ? 'Payment was successful.' : 'Payment was unsuccessful.',
            'code' => $status ? 0 : -1,
            'input' => $request,
            'output' => [],
        ];
    }
}
