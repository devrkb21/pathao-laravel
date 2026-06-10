<?php

namespace devrkb21\PathaoLaravel\DataDTO;

use devrkb21\PathaoLaravel\Requests\PathaoAccessTokenRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AccessTokenDTO
{
    /**
     * This will standardize the data that from a request
     */
    public function fromRequest(PathaoAccessTokenRequest $request): array
    {
        return [
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
        ];
    }

    /**
     * This will standardize the data that from a request
     */
    public function fromAccessTokenResponse(array $data): array
    {
        $secret_token = (string) Str::uuid();
        $token = Arr::get($data, 'access_token');
        $refresh_token = Arr::get($data, 'refresh_token') ?: '';
        $expires_in = time() + Arr::get($data, 'expires_in');

        return [
            'secret_token' => $secret_token,
            'token' => $token,
            'refresh_token' => $refresh_token,
            'expires_in' => $expires_in,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * This will standardize the data that from a request
     */
    public function fromRefreshTokenResponse(array $data): array
    {
        $token = Arr::get($data, 'access_token');
        $refresh_token = Arr::get($data, 'refresh_token') ?: '';
        $expires_in = time() + Arr::get($data, 'expires_in');

        return [
            'token' => $token,
            'refresh_token' => $refresh_token,
            'expires_in' => $expires_in,
            'updated_at' => now(),
        ];
    }
}
