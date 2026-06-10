<?php

namespace devrkb21\PathaoLaravel\APIBase;

use devrkb21\PathaoLaravel\DataDTO\AccessTokenDTO;
use devrkb21\PathaoLaravel\Services\DataServiceOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PathaoAuth extends PathaoBaseAPI
{
    public $secret_token;

    public $table_name;

    public $pathao_token_data;

    public function __construct()
    {
        parent::__construct();

        if ($this->pathao_token_data) {
            $checkAccessTokenIsValid = $this->checkAccessTokenIsValid();
            // If access token is not valid request for a new token.
            if (! $checkAccessTokenIsValid) {
                $this->getNewAccesstoken();
            }
        }
    }

    /**
     * Issue a token from Pathao Courier
     */
    public function getAccessToken(array $cred): DataServiceOutput
    {
        $url = 'aladdin/api/v1/external/login';

        $API_response = $this->Pathao_API_Response(false, $url, Request::METHOD_POST, $cred);

        $data = Arr::get($API_response, 'data') ?: [];
        $message = Arr::get($API_response, 'message') ?: null;
        $is_success = $this->isSuccessfulResponse(Arr::get($API_response, 'status'));
        $status_code = Arr::get($API_response, 'data.code') ?: [];

        if ($is_success) {
            $response = (new AccessTokenDTO)->fromAccessTokenResponse($data);

            if ($this->pathao_token_data) {
                DB::table($this->table_name)
                    ->where('secret_token', '=', $this->pathao_token_data->secret_token)
                    ->update($response);
            } else {
                DB::table($this->table_name)->insert($response);
            }

            // Prevent stale in-memory token data
            $this->pathao_token_data = (object) $response;

            $message = 'Token stored successfully';
            $data['secret_token'] = Arr::get($response, 'secret_token');
        }

        return new DataServiceOutput($data, $message, $is_success, $status_code);
    }

    /**
     * This function will check the current token is valid or not
     */
    private function checkAccessTokenIsValid(): bool
    {
        return $this->pathao_token_data->expires_in >= time();
    }

    /**
     * This function will return the remaining days of expiration
     * it will return both days left and the expected date.
     */
    public function getAccessTokenExpiryDaysLeft(): DataServiceOutput
    {
        $days_left = ceil(($this->pathao_token_data->expires_in - time()) / 86400);
        $expected_date = now()->addDays($days_left)->toDateString();

        $response = [
            'days_left' => $days_left,
            'expected_expiration_date' => $expected_date,
        ];
        $message = $days_left.' days left for Token expiration';

        return new DataServiceOutput($response, $message);
    }

    /**
     * Get new access token if the token is outdated.
     */
    public function getNewAccesstoken(): DataServiceOutput
    {
        $cred = [
            'client_id' => config('pathao.pathao_client_id'),
            'client_secret' => config('pathao.pathao_client_secret'),
        ];

        return $this->getAccessToken($cred);
    }
}
