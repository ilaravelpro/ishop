<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iShop\iApp\Http\Controllers\API\v1;


use iLaravel\Core\iApp\Exceptions\iException;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;
use Illuminate\Support\Facades\DB;

class OrderController extends \iLaravel\Core\iApp\Http\Controllers\API\ApiController
{
    use Order\ActionItem;
    public function cancel(Request $request, $record)
    {
        if ($record = $this->model::findBySerial($record)) {
            $record->cancel();
            $this->statusMessage = 'با موفقیت موجودی کتاب ها بروزرسانی شدند.';
        }else
            throw new iException('اطلاعاتی یافت نشد.');
        $record = $this->model::find($record->id);
        $record->calc();
        $record->save();
        return $this->_show($request, $record);
    }
    public function send_sms(Request $request, $record)
    {
        if ($record = $this->model::findBySerial($record)) {
            if (@$record->creator->mobile->text)
                isms_send("modals.orders.status." . ($request->status?:'completed'), @$record->creator->mobile->text, [
                    'number' => @$record->invoice_number?:@$record->number,
                    'invoice' => @$record->invoice_number,
                ]);
            $this->statusMessage = 'باموفقیت پیامک ارسال وضعیت سفارش انجام گردید.';
        }else
            throw new iException('اطلاعاتی یافت نشد.');
        return $this->_show($request, $record);
    }
    public function statics(Request $request)
    {

        $month = $this->model::select(DB::raw('created_at, count(id) as countb, sum(payment_total) as totals'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->groupBy('new_date')->get()->map(function ($item) {
                return [
                    'label' => jdate($item->new_date . '-01')->format('F Y'),
                    'count' => $item->countb,
                    'totals' => $item->totals,
                ];
            });
        return ['data' => [
            'month' => $month
        ]];
    }
}
