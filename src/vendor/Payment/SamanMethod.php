<?php

namespace iLaravel\iShop\Vendor\Payment;


class SamanMethod extends TestMethod
{

    public $model;
    public $configs;

    public $mid = null;
    public $order = null;

    public function __construct($model)
    {
        $this->model = $model;
        $this->mid = @$this->model->authenticate['mid'];
    }

    public static function fast($model)
    {
        return (new static($model));
    }

    public function request($order, $log, $amount, $callback, $description, $currency, $mobile = null, $email = null)
    {
        $input = [
            'amount' => $amount,
            'callback' => $callback,
            'description' => $description,
            'currency' => $currency,
            'mobile' => $mobile,
            'email' => $email,
        ];
        $soap = new \SoapClient('https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL', [
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'ciphers' => 'DEFAULT:!DH',
                ],
            ]),]);
        $token = $soap->RequestToken($this->mid, $log->number, (string)($log->amount * 10), "0", "0", "0", "0", "0", "0", (string)$order->serial, "", "0", $callback);
        return [
            'status' => true,
            'endpoint' => route('payment.providers.test.show', ['payment' => $log->serial]),
            'transaction_id' => $token,
            'message' => _t("Token created successfully."),
            'code' => 0,
            'input' => $input,
            'output' => ['token' => $token],
        ];
    }

    public function redirect($payment)
    {
        return redirect_post('https://sep.shaparak.ir/payment.aspx', ['Token' => urlencode($payment->transaction_id), 'RedirectURL' => $payment->send_request['callback']]);
    }

    public function verify($order, $log)
    {
        $client = new \SoapClient('https://verify.sep.ir/Payments/ReferencePayment.asmx?WSDL', [
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'ciphers' => 'DEFAULT:!DH',
                ],
            ]),
        ]);
        $refnum = request('RefNum');
        $result = $client->VerifyTransaction($refnum, $this->mid);
        $status = $result > 0;
        return [
            'status' => $status,
            'state' => $status ? 'successful' : 'unsuccessful',
            'reference_id' => $refnum,
            'message' => $status ? 'Payment was successful.' : 'Payment was unsuccessful.',
            'code' => $result > 0 ? 0 : -1,
            'input' => request()->all(),
            'output' => $result,
        ];
    }
}
