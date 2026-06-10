<?php

namespace devrkb21\PathaoLaravel\Services;

class StandardResponseService
{
    /**
     * This will standardize the data for output
     *
     * @param  mixed  $response
     */
    public static function RESPONSE_OUTPUT($response): array
    {
        return [
            'data' => $response->getData(),
            'message' => $response->getMessage(),
            'status' => $response->getStatusCode(),
        ];
    }
}
