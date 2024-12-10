<?php

namespace iLaravel\iShop\Vendor\Payment;


class ParsianMethod extends TestMethod
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
            'LoginAccount' => $this->mid,
            'Amount' => (int)($log->amount * 10),
            'OrderId' => time() . $log->id,
            'AdditionalData' => '',
            'CallBackUrl' => $callback,
        ];
        $soap = new \SoapClient('https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl', [
            'encoding' => 'UTF-8',
        ]);
        $result = $soap->SalePaymentRequest(["requestData" => $input]);
        $status = isset($result->SalePaymentRequestResult) && $result->SalePaymentRequestResult->Status == 0 && $result->SalePaymentRequestResult->Token > 0;
        return [
            'status' => $status,
            'endpoint' => route('payment.providers.test.show', ['payment' => $log->serial]),
            'transaction_id' => $result->SalePaymentRequestResult->Token,
            'message' => $status ? _t("Token created successfully.") : _t("Token created failed."),
            'code' => 0,
            'input' => $input,
            'output' => (array)$result->SalePaymentRequestResult,
        ];
    }

    public function redirect($payment)
    {
        return redirect('https://pec.shaparak.ir/NewIPG/?' . http_build_query(['token' => $payment->transaction_id]));
    }

    public function verify($order, $log)
    {
        $input = [
            'LoginAccount' => $this->mid,
            'Token' => request('Token'),
        ];
        $soap = new \SoapClient('https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?wsdl', [
            'encoding' => 'UTF-8',
        ]);
        $result = $soap->ConfirmPayment(["requestData" => $input]);
        $status = isset($result->ConfirmPaymentResult) && $result->ConfirmPaymentResult->Status == 0 && $result->ConfirmPaymentResult->RRN > 0;
        $refnum = $result->ConfirmPaymentResult->RRN;
        return [
            'status' => $status,
            'state' => $status ? 'successful' : 'unsuccessful',
            'reference_id' => $refnum,
            'message' => $status ? 'Payment was successful.' : 'Payment was unsuccessful.',
            'code' => $status ? 0 : -1,
            'input' => request()->all(),
            'output' => (array)$result->ConfirmPaymentResult,
        ];
    }
}
