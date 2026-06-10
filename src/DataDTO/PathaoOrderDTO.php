<?php

namespace devrkb21\PathaoLaravel\DataDTO;

use devrkb21\PathaoLaravel\Requests\PathaoOrderPriceCalculationRequest;
use devrkb21\PathaoLaravel\Requests\PathaoOrderRequest;
use devrkb21\PathaoLaravel\Requests\PathaoUserSuccessRateRequest;

class PathaoOrderDTO
{
    public function fromOrderRequest(PathaoOrderRequest $request)
    {
        $payload = [
            'store_id' => $request['store_id'],
            'merchant_order_id' => $request['merchant_order_id'],
            'recipient_name' => $request['recipient_name'],
            'recipient_phone' => $request['recipient_phone'],
            'recipient_address' => $request['recipient_address'],
            'delivery_type' => $request['delivery_type'],
            'item_type' => $request['item_type'],
            'special_instruction' => $request['special_instruction'],
            'item_quantity' => $request['item_quantity'],
            'item_weight' => $request['item_weight'],
            'amount_to_collect' => $request['amount_to_collect'],
            'item_description' => $request['item_description'],
        ];

        if (! empty($request['recipient_city'])) {
            $payload['recipient_city'] = (int) $request['recipient_city'];
        }
        if (! empty($request['recipient_zone'])) {
            $payload['recipient_zone'] = (int) $request['recipient_zone'];
        }
        if (! empty($request['recipient_area'])) {
            $payload['recipient_area'] = (int) $request['recipient_area'];
        }
        if (! empty($request['recipient_secondary_phone'])) {
            $payload['recipient_secondary_phone'] = $request['recipient_secondary_phone'];
        }

        return $payload;
    }

    public function fromPriceCalculationRequest(PathaoOrderPriceCalculationRequest $request)
    {
        return [
            'delivery_type' => $request['delivery_type'],
            'item_type' => $request['item_type'],
            'item_weight' => $request['item_weight'],
            'recipient_city' => $request['recipient_city'],
            'recipient_zone' => $request['recipient_zone'],
            'store_id' => $request['store_id'],
        ];
    }

    public function fromUserSuccessRate(PathaoUserSuccessRateRequest $request)
    {
        return [
            'phone' => $request['phone'],
        ];
    }
}
