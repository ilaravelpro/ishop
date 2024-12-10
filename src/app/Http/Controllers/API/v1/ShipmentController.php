<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\API\v1;



use iLaravel\Core\iApp\Exceptions\iException;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

class ShipmentController extends \iLaravel\Core\iApp\Http\Controllers\API\ApiController
{

    public function send_sms(Request $request, $record)
    {
        if ($record = $this->model::findBySerial($record)) {
            if (@$record->order->creator->mobile->text)
                isms_send("modals.shipments.status.sent", @$record->order->creator->mobile->text, [
                    'number' => @$record->order->invoice_number?:@$record->order->number,
                    'invoice' => @$record->order->invoice_number,
                    'code' => @$record->tracking_id,
                    'url' => @$record->method->service->getUrl($record->tracking_id)
                ]);
            $this->statusMessage = 'باموفقیت پیامک ارسال اطلاعات حمل‌ونقل انجام گردید.';
        }else
            throw new iException('اطلاعاتی یافت نشد.');
        return $this->_show($request, $record);
    }
}
