<?php

namespace devrkb21\PathaoLaravel\Requests;

/**
 * @property string $client_id
 * @property string $client_secret
 */
class PathaoAccessTokenRequest extends BasePathaoRequest
{
    public function rules()
    {
        return [
            'client_id' => [
                'required',
                'string',
            ],
            'client_secret' => [
                'required',
                'string',
            ],
        ];
    }
}
