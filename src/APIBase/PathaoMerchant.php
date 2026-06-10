<?php

namespace devrkb21\PathaoLaravel\APIBase;

use devrkb21\PathaoLaravel\Services\DataServiceOutput;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Request;

class PathaoMerchant extends PathaoBaseAPI
{
    /**
     * Get Merchant Short Profile Info
     */
    public function get_merchant_info(): DataServiceOutput
    {
        $url = 'aladdin/api/v1/user/short-info';
        $API_response = $this->Pathao_API_Response(true, $url, Request::METHOD_GET);

        $data = Arr::get($API_response, 'data.data') ?: [];
        $message = Arr::get($API_response, 'data.message') ?: null;
        $is_success = $this->isSuccessfulResponse(Arr::get($API_response, 'status'));
        $status_code = Arr::get($API_response, 'data.code') ?: [];

        return new DataServiceOutput($data, $message, $is_success, $status_code);
    }
}
