<?php

namespace iLaravel\iShop\iApp\Http\Controllers\API\v1\Order;

use iLaravel\Core\iApp\Exceptions\iException;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;
trait ActionItem
{
    public function toPay(Request $request, $record)
    {
        if ($record = $this->model::findBySerial($record)) {
            $payment = $record->payment_gateway?:(imodal('Payment')::findBySerial($request->payment_id)?:imodal('Payment')::where('status', 'active')->first());
            $result = \iLaravel\iPayment\Vendor\PaymentService::send($record, $record->payment_total, [], $payment);
            if ($result['status']) {
                $this->statusMessage = 'Payment link was successfully created.';
                return ['data' => $result];
            } else throw new iException($result['message']);
        } else
            throw new iException('No information found.');
    }

    public function gateway(Request $request, $record)
    {
        $payment_model = imodal('Payment');
        if ($record = $this->model::findBySerial($record)) {
            if ($payment = $payment_model::findBySerial($request->payment)) {
                $record->payment_gateway_id = $payment->id;
                $this->statusMessage = 'The payment method was successfully applied.';
                $record->calc();
                $record->save();
            } else
                throw new iException('The payment method is invalid.');
        } else
            throw new iException('No information found.');
        return $this->_show($request, $record);
    }

    public function shipping(Request $request, $record)
    {
        $shipping_model = imodal('ShippingMethod');
        if ($shipping_model) {
            if ($record = $this->model::findBySerial($record)) {
                if ($shipping = $shipping_model::findBySerial($request->shipping)) {
                    $record->shipping_method_id = $shipping->id;
                    $this->statusMessage = 'The sending method was successfully applied.';
                    $record->calc();
                    $record->save();
                } else
                    throw new iException('The sending method is invalid.');
            } else
                throw new iException('No information found.');
        } else
            throw new iException('Product shipping system not found.');
        return $this->_show($request, $record);
    }

    public function address(Request $request, $record)
    {
        $address_model = imodal('Address');
        $shipping_model = imodal('ShippingMethod');
        if ($request->type == 'shipping' && !$shipping_model) throw new iException('Product shipping system not found.');
        if ($record = $this->model::findBySerial($record)) {
            if ($address = $address_model::findBySerial($request->address)) {
                if ($shipping_model && $request->type == 'shipping' && $record->shipping_id == $record->billing_id) {
                    $record->billing_id = $address->id;
                    $record->creator->update(["billing_id" => $address->id]);
                }
                $record->{"{$request->type}_id"} = $address->id;
                $record->creator->update(["{$request->type}_id" => $address->id]);
                $this->statusMessage = 'The address was successfully applied.';
                $record->calc();
                $record->save();
            } else
                throw new iException('The address is invalid.');
        } else
            throw new iException('No information found.');
        return $this->_show($request, $record);
    }

    public function discountUse(Request $request, $record)
    {
        $discount_model = imodal('Discount');
        if ($record = $this->model::findBySerial($record)) {
            if ($discount = $discount_model::where('code', $request->code)
                ->where(function ($q) {
                    $q->where('start_at', '>=', now()->format('Y-m-d H:i:S'))
                        ->orWhereNull('start_at');
                })
                ->where(function ($q) {
                    $q->where('end_at', '<=', now()->format('Y-m-d H:i:S'))
                        ->orWhereNull('end_at');
                })->first()) {
                if (!($discount->price_min > 0 && $discount->price_min > $record->invoice_total)) {
                    if (!($discount->quantity_min > 0 && $record->items->sum('count') > $discount->quantity_min)) {
                        $record->discount_id = $discount->id;
                        $this->statusMessage = 'The discount code was successfully applied.';
                        $record->calc();
                        $record->save();
                    } else
                        throw new iException(sprintf("Your cart must contain more than %s.", $discount->quantity_min));
                } else
                    throw new iException(sprintf("Your cart total must be greater than %s.", number_format($discount->price_min) . ' تومان'));

            } else
                throw new iException('The discount code is invalid.');
        } else
            throw new iException('No information found.');
        return $this->_show($request, $record);
    }

    public function discountRemove(Request $request, $record)
    {
        if ($record = $this->model::findBySerial($record)) {
            $record->discount_id = null;
            $this->statusMessage = 'The discount code was successfully deleted.';
            $record->calc();
            $record->save();
        } else
            throw new iException('No information found.');
        return $this->_show($request, $record);
    }

    public function actionItem(Request $request, $record, $action, $arg)
    {
        $message = [
            'append' => 'The desired product was successfully added to the shopping cart.',
            'decrease' => 'The desired product was successfully decreased.',
            'increase' => 'The desired product was successfully increased.',
            'remove' => 'The desired product was successfully removed.',
        ];
        $this->statusMessage = $message[$action];
        if ($record = $this->model::findBySerial($record)) {
            $result = $record->actionItem($action, $arg, $request->count, $request->price_index);
            if (!$result->status) throw new iException($result->data);
        } else
            throw new iException('No information found.');
        $record = $this->model::find($record->id);
        $record->calc();
        $record->save();
        return $this->_show($request, $record);
    }

    public function append(Request $request, $record, $product)
    {
        return $this->actionItem($request, $record, 'append', $product);
    }

    public function decrease(Request $request, $record, $index)
    {
        return $this->actionItem($request, $record, 'decrease', $index);
    }

    public function increase(Request $request, $record, $index)
    {
        return $this->actionItem($request, $record, 'increase', $index);
    }

    public function remove(Request $request, $record, $index)
    {
        return $this->actionItem($request, $record, 'remove', $index);
    }

    public function before_update($request, $arg1, $arg2, $arg3, $arg4)
    {
        $arg2->change_stock($request->count, $request->product_id, $request->price_id);
    }

}
