<?php

namespace devrkb21\PathaoLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array GET_ACCESS_TOKEN_EXPIRY_DAYS_LEFT()
 * @method static array GET_CITIES()
 * @method static array GET_ZONES(int $city_id)
 * @method static array GET_AREAS(int $zone_id)
 * @method static array GET_STORES(int $page = 1)
 * @method static array CREATE_STORE(\devrkb21\PathaoLaravel\Requests\PathaoStoreRequest $request)
 * @method static array VIEW_ORDER(string $consignment_id)
 * @method static array CREATE_ORDER(\devrkb21\PathaoLaravel\Requests\PathaoOrderRequest $request)
 * @method static array GET_PRICE_CALCULATION(\devrkb21\PathaoLaravel\Requests\PathaoOrderPriceCalculationRequest $request)
 * @method static array GET_USER_SUCCESS_RATE(\devrkb21\PathaoLaravel\Requests\PathaoUserSuccessRateRequest $request)
 * @method static array CREATE_ORDERS_BULK(array $orders)
 * @method static array GET_ZONES_BULK()
 * @method static array GET_AREAS_BULK()
 * @method static array GET_MERCHANT_INFO()
 *
 * @see \devrkb21\PathaoLaravel\PathaoLaravel
 */
class PathaoLaravel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \devrkb21\PathaoLaravel\PathaoLaravel::class;
    }
}
